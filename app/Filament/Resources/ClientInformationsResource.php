<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Imports\ClientInformationsImporter;
use App\Filament\Resources\ClientInformationsResource\Pages;
use App\Filament\Resources\ClientInformationsResource\RelationManagers;
use App\Livewire\DocumentFormActionComponent;
use App\Livewire\OpenLinkNewTabButtonComponent;
use App\Models\ClientInformations;
use App\Models\Documents;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Filament\Tables\Actions\ViewAction;

class ClientInformationsResource extends Resource
{
    protected static ?string $model = ClientInformations::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ...
            ]);
    }

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
//                Tables\Columns\TextColumn::make('documents')
//                    ->disabledClick()
//                    ->state(function ( ClientInformations $record) {
//                        return new HtmlString(view('custom_column.document_action', [
//                            'record' => $record,
//                            'documents'=>Documents::all()
//                        ])->render());
//                    }),
//                Tables\Columns\SelectColumn::make('document')
//                    ->options(Documents::all()->pluck('name','id')->toArray())
//                    ->beforeStateUpdated(function ($record, $state,Component $livewire) {
//                        $this->dispatch('open-post-edit-modal', post: $record->getKey());
//                       dd($record,$state);
//                    }),
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
                    Tables\Actions\ViewAction::make()->label('View Details')->button(),
                    Tables\Actions\Action::make('document')
                        ->button()
                    ->form(function(Form $form,array $data,ClientInformations $record){
                        return $form->schema([
                        Select::make('document')
                            ->label('Select Document')
                            ->native(false)
                            ->options(Documents::all()->pluck('name','id')->toArray())
                            ->searchable()
                            ->required(),
                        ToggleButtons::make('action')
                            ->options([
                                'view' => 'View',
                                'download' => 'Download',
                            ])
                            ->icons([
                                'view' => 'heroicon-o-eye',
                                'download' => 'heroicon-o-arrow-down-tray',
                            ])
                            ->inline()
                            ->columns(2)
                            ->default('view')
                            ->required(),
                            ]);
                    })
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                        ->action(function (array $data, ClientInformations $record, Component $livewire) {
                           $livewire->dispatch('open-link-new-tab-event',route('docx_to_pdf', [$record,$data['document'],$data['action']=='view'?1:0]));
                        })
                        ->modalWidth(MaxWidth::Small)
//                ActionGroup::make(
//                    array_merge(Documents::all()->map(function($document){
//                        return  Tables\Actions\Action::make('view_'.$document->name)
//                            ->url(fn (ClientInformations $record): string => route('docx_to_pdf', [$record,$document,1]))
//                            ->label($document->name)
//                            ->icon('heroicon-m-eye')
//                            ->openUrlInNewTab();
//                    })->toArray(),
//                        Documents::all()->map(function($document){
//                            return  Tables\Actions\Action::make('view_'.$document->name)
//                                ->url(fn (ClientInformations $record): string => route('docx_to_pdf', [$record,$document,0]))
//                                ->label($document->name)
//                                ->icon('heroicon-m-arrow-down-tray')
//                                ->openUrlInNewTab();
//                        })->toArray()
//                    )
//                )
//                    ->label('Documents')
//                    ->icon('heroicon-m-ellipsis-vertical')
//                    ->size(ActionSize::Small)
//                    ->color('primary')
//                    ->button()
            ]
                , position: ActionsPosition::BeforeCells)
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
//            'edit' => Pages\EditClientInformations::route('/{record}/edit'),
        ];
    }
}
