<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Imports\ContactImporter;
use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Filament\Resources\Maintenance\CompaniesResource;
use App\Models\ClientInformations;
use App\Models\Companies;
use App\Models\Documents;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Set;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Homeful\Contacts\Actions\PersistContactAction;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contacts\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
//use RLI\Booking\Imports\Cornerstone\OSReportsImport;
use App\Imports\OSImport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use function PHPUnit\Framework\throwException;

class ContactResource extends Resource
{
    protected static ?string $label ='Contacts Information';
    protected static ?string $model = Contact::class;
    protected static ?string $recordTitleAttribute ='reference_code';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Personal')
                            ->schema([
                                Fieldset::make('Personal Info')->schema([
                                    TextEntry::make('first_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('middle_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('last_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('sex')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('nationality')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('date_of_birth')
                                        ->weight(FontWeight::Bold)
                                        ->date(),
                                    TextEntry::make('email')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('mobile')
                                        ->label('Mobile Number')
                                        ->weight(FontWeight::Bold),
                                ]),
                                Fieldset::make('Spouse Info')->schema([
                                    TextEntry::make('spouse.first_name')
                                        ->label('First Name'),
                                    TextEntry::make('spouse.middle_name')
                                        ->label('Middle Name'),
                                    TextEntry::make('spouse.last_name')
                                        ->label('Last Name'),
                                ]),
                            ]),
                        Tabs\Tab::make('Employment')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Co-Borrowers')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Order')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->activeTab(1)->columnSpanFull(),

                ])->inlineLabel(); // TODO: Change the autogenerated stub
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->label('OS Report')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(1024*12)
                    ->storeFiles(false),
//                Forms\Components\TextInput::make('reference_code')
//                    ->required(),
//                Forms\Components\TextInput::make('first_name')
//                    ->required(),
//                Forms\Components\TextInput::make('middle_name')
//                    ->required(),
//                Forms\Components\TextInput::make('last_name')
//                    ->required(),
//                Forms\Components\TextInput::make('civil_status')
//                    ->required(),
//                Forms\Components\TextInput::make('sex')
//                    ->required(),
//                Forms\Components\TextInput::make('nationality')
//                    ->required(),
//                Forms\Components\DatePicker::make('date_of_birth')
//                    ->required(),
//                Forms\Components\TextInput::make('email')
//                    ->email()
//                    ->required(),
//                Forms\Components\TextInput::make('mobile')
//                    ->required(),
//                Forms\Components\Textarea::make('spouse')
//                    ->required()
//                    ->columnSpanFull(),
//                Forms\Components\Textarea::make('addresses')
//                    ->columnSpanFull(),
//                Forms\Components\Textarea::make('employment')
//                    ->columnSpanFull(),
//                Forms\Components\Textarea::make('co_borrowers')
//                    ->columnSpanFull(),
//                Forms\Components\Textarea::make('order')
//                    ->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->poll('10')
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->defaultSort('id','desc')
//            ->query(
//                Contact::query()
//                    ->whereIn('project',Auth::user()->projects()->pluck('description'))
//                    ->whereIn('location',Auth::user()->locations()->pluck('description'))
//            )
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
                Tables\Actions\ViewAction::make()->label('View Details')->button(),
                Tables\Actions\Action::make('document')
                    ->button()
                    ->form([
                        Select::make('document')
                            ->label('Select Document')
                            ->native(false)
                            ->options(
                                Documents::all()->mapWithKeys(function ($document) {
                                    return [$document->id => $document->name];
                                })->toArray()
                            )
                            ->multiple()
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
                    ])
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                    ->action(function (array $data, Contact $record, Component $livewire) {

                        foreach ($data['document'] as $d){
                        $livewire->dispatch('open-link-new-tab-event',route('contacts_docx_to_pdf', [$record,$d,$data['action']=='view'?1:0,$record->last_name]));
                        }
                    })
                    ->modalWidth(MaxWidth::Small)
            ], position: ActionsPosition::BeforeCells)
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
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(1024*12)
                            ->storeFiles(false)
                            ->live(),

                        Forms\Components\Placeholder::make('error')
                            ->label('')
                            ->content('')

                    ])
                    ->action(function (array $arguments, $form, $data,Set $set): void {
//                        Excel::import(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
                        try {
                            Excel::queueImport(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
                        } catch (\Exception $e) {
                            if (property_exists($e, 'validator')) {
                                $messages = $e->validator->messages()->toArray();

                                $errorMessages = collect($messages)->map(function($message, $field) {
                                    return "$field: " . implode(', ', $message) . '<br>';
                                })->implode('');


                                Log::error('Excel Import failed: ' . $errorMessages);
                                Notification::make()
                                    ->title('Excel Import failed:')
                                    ->danger()
                                    ->persistent()
                                    ->body($errorMessages)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Excel Import failed:')
                                    ->danger()
                                    ->persistent()
                                    ->body($e->getMessage())
                                    ->send();
                                Log::error('Excel Import failed: ' . $e->getMessage());
                            }
                        }
                    })

            ])->filters([
//                SelectFilter::make('project')
//                    ->multiple()
//                    ->options(
//                        Auth::user()->projects()->get()->mapWithKeys(function ($item,$keys) {
//                            return [$item->description => $item->description];
//                        })->toArray()
//                    )->columnSpan(2),
//                SelectFilter::make('location')
//                    ->multiple()
//                    ->options(
//                        Auth::user()->locations()->get()->mapWithKeys(function ($item,$keys) {
//                            return [$item->description => $item->description];
//                        })->toArray()
//                    )->columnSpan(2)
            ], layout: FiltersLayout::AboveContent);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
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
//            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
