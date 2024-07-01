<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ContactImporter;
use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Homeful\Contacts\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;
//use RLI\Booking\Imports\Cornerstone\OSReportsImport;
use App\Imports\OSImport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference_code')
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('middle_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('civil_status')
                    ->required(),
                Forms\Components\TextInput::make('sex')
                    ->required(),
                Forms\Components\TextInput::make('nationality')
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('mobile')
                    ->required(),
                Forms\Components\Textarea::make('spouse')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('addresses')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('employment')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('co_borrowers')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('order')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('civil_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sex')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
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
            ])->headerActions([
                Tables\Actions\Action::make('Import OS Report')
                    ->label('Import OS Report')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('OS Report')
                            ->storeFiles(false),
                    ])
                    ->action(function (array $arguments, $form, $data): void {
                        Excel::import(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
                    })

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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
