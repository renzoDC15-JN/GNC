<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $navigationGroup = 'Admin';


    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';
    protected static ?string $recordTitleAttribute = 'name';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

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
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->hintActions(
                        [
                            Action::make('Generate')
                            ->icon('heroicon-m-arrow-path')
                            ->action(function (Set $set, $state) {
                                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                $charactersLength = strlen($characters);
                                $randomString = '';

                                for ($i = 0; $i < 16; $i++) {
                                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                                }

                                $set('password', $randomString);
                            }),
                        ]
                    )
                    ->columnSpan(4),
            ])
            ->columns(12)->columnSpan(2),

            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()->native(false),
                            Select::make('companies')
                                ->label('Companies')
                                ->multiple()
                                ->relationship('companies','description')
                                ->preload()
                                ->columnSpanFull()
                                ->columns(12)->native(false),
                            Select::make('projects')
                                ->label('Projects')
                                ->multiple()
                                ->relationship('projects','description')
                                ->preload()
                                ->columnSpanFull()
                                ->columns(12)->native(false),
                            Select::make('locations')
                                ->label('Locations')
                                ->multiple()
                                ->relationship('locations','description')
                                ->preload()
                                ->columnSpanFull()
                                ->columns(12)->native(false),
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
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->columns([
                TextColumn::make('id')
                ->sortable()
                ->searchable(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->sortable()
                    ->searchable(),

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
