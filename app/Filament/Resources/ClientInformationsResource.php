<?php

namespace App\Filament\Resources;

use App\Filament\Imports\ClientInformationsImporter;
use App\Filament\Resources\ClientInformationsResource\Pages;
use App\Filament\Resources\ClientInformationsResource\RelationManagers;
use App\Models\ClientInformations;
use App\Models\Documents;
use App\Models\Projects;
use BladeUI\Icons\Components\Icon;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ImportAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ClientInformationsResource extends Resource
{
    protected static ?string $model = ClientInformations::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('project')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('property_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phase')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('block')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lot')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_civil_status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_nationality')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_tin')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('buyer_spouse_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mrif_fee')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reservation_rate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->query(
                ClientInformations::query()
                    ->whereIn('project',Auth::user()->projects()->pluck('description'))
                    ->whereIn('location',Auth::user()->locations()->pluck('description'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('project')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phase')
                    ->searchable(),
                Tables\Columns\TextColumn::make('block')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_civil_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_tin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer_spouse_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mrif_fee')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reservation_rate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_by')
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
                ActionGroup::make(
                    array_merge(Documents::all()->map(function($document){
                        return  Tables\Actions\Action::make('view_'.$document->name)
                            ->url(fn (ClientInformations $record): string => route('docx_to_pdf', [$record,$document,1]))
                            ->label($document->name)
                            ->icon('heroicon-m-eye')
                            ->openUrlInNewTab();
                    })->toArray(),
                        Documents::all()->map(function($document){
                            return  Tables\Actions\Action::make('view_'.$document->name)
                                ->url(fn (ClientInformations $record): string => route('docx_to_pdf', [$record,$document,0]))
                                ->label($document->name)
                                ->icon('heroicon-m-arrow-down-tray')
                                ->openUrlInNewTab();
                        })->toArray()
                    )
                )
                    ->label('Documents')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button()
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([

            ])->filters([
                SelectFilter::make('project')
                    ->multiple()
                    ->options(
                        Auth::user()->projects()->get()->mapWithKeys(function ($item,$keys) {
                        return [$item->description => $item->description];
                        })->toArray()
                    )->columnSpan(2),
                SelectFilter::make('location')
                    ->multiple()
                    ->options(
                        Auth::user()->locations()->get()->mapWithKeys(function ($item,$keys) {
                            return [$item->description => $item->description];
                        })->toArray()
                    )->columnSpan(2)
            ], layout: FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->deselectAllRecordsWhenFiltered(false);
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
            'index' => Pages\ListClientInformations::route('/'),
            'create' => Pages\CreateClientInformations::route('/create'),
            'edit' => Pages\EditClientInformations::route('/{record}/edit'),
        ];
    }
}
