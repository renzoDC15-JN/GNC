<?php

namespace App\Livewire;

use App\Models\UpdateLog;
use Carbon\Carbon;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
class UpdateLogComponent extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;


    public Model $model;
    public function table(Table $table): Table
    {
        return $table
            ->heading('Update Logs')
            ->query(UpdateLog::where('loggable_id',$this->model->id))
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('updated_at')
                    ->formatStateUsing(function (string $state, Model $record) {
                        $date = Carbon::parse($state);
                        $formattedDate = $date->format('F j, Y');
                        $formattedTime = $date->format('g:i A');
                        $timeAgo = $date->diffForHumans(); // 1 hour ago
                        $user = $record->user==null?'':$record->user->name;
                        return $formattedDate . '<br>' . $formattedTime . '<br>'.$user. '<br><small>' . $timeAgo . '</small>';
                    })
                    ->html(),
                TextColumn::make('field')->grow(false),
                TextColumn::make('from')->wrap()->grow(false),
                TextColumn::make('to')->wrap()->grow(),
//                TextColumn::make('to')->grow(false),
//                    ->formatStateUsing(function (string $state, Model $record) {
//                        return $state.': '.$record->from . ' => ' . $record->to;
//                    })
//                    ->grow()
//                    ->alignment(Alignment::Start)
//                    ->wrap(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    public function render()
    {
        return view('livewire.update-log-component');
    }
}
