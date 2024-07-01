<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $navigationGroup = 'User Management';


    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    public static function form(Form $form): Form
    {
        $form_user = $form->model;
        return $form
        ->schema([
            Forms\Components\Section::make()
            ->schema([
                TextInput::make('email')
                ->required()
                ->unique(ignoreRecord: true)
                ->columnSpan(4),
                TextInput::make('name')
                    ->required()
                    ->columnSpan(8),

            ])
            ->columns(12)->columnSpan(2),

            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload(),
                            Select::make('projects')
                                ->label('Projects')
                                ->multiple()
                                ->relationship('projects','description')
                                ->preload()
                                ->columnSpanFull()
                                ->columns(12),
                            Select::make('locations')
                                ->label('locations')
                                ->multiple()
                                ->relationship('locations','description')
                                ->preload()
                                ->columnSpanFull()
                                ->columns(12),
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
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.name'),

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
            // RelationGroup::make('', [
            //     ParticipantsRelationManager::class,
            //     LogsRelationManager::class,
            // ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
