<?php

namespace App\Filament\Resources\Maintenance;

use App\Filament\Resources\Maintenance\DocumentsResource\Pages;
use App\Filament\Resources\Maintenance\DocumentsResource\RelationManagers;
use App\Livewire\DocumentPreviewComponent;
use App\Models\Documents;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use phpDocumentor\Reflection\Types\False_;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\Livewire;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class DocumentsResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description'),
                        FileUpload::make('file_attachment')
                            ->directory('documents')
                            ->preserveFilenames(),
                        Forms\Components\Select::make('fields')
                            ->options(
                                collect(Schema::getColumnListing('contacts'))->mapWithKeys(function ($item, $key) {
                                    return [$item=>$item];
                                })->toArray()
                            )->multiple(),

                    ])->columns(1)->columnSpan(4),

                    FilamentJsonColumn::make('data')->columnSpan(8),
                ])->columns(12)->columnSpanFull(),
                Forms\Components\Section::make()->schema([
                    Livewire::make(DocumentPreviewComponent::class)
                        ->key(Carbon::now()->format('Y-m-d H:i:s'))
                        ->columnSpanFull()
                ])->columnSpan(8),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocuments::route('/create'),
            'edit' => Pages\EditDocuments::route('/{record}/edit'),
        ];
    }
}
