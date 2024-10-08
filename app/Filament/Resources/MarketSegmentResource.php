<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketSegmentResource\Pages;
use App\Filament\Resources\MarketSegmentResource\RelationManagers;
use App\Models\MarketSegment;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class MarketSegmentResource extends Resource
{
    protected static ?string $model = MarketSegment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $recordTitleAttribute ='description';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->columnSpan(4),
                        TextInput::make('description')
                            ->label('Name')
                            ->required()
                            ->columnSpan(8),
                        Toggle::make('active')
                            ->required(),

                    ])
                    ->columns(12)->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        // Forms\Components\Section::make()
                        //     ->schema([

                        //         Placeholder::make('created_at')
                        //     ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                        //     Placeholder::make('updated_at')
                        //         ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                        // ]),
                        Forms\Components\Section::make()
                            ->schema([

                                Placeholder::make('created_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                                Placeholder::make('updated_at')
                                    ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMarketSegments::route('/'),
        ];
    }
}
