<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Exports\ApplicationsExport;
use App\Filament\Resources\ApplicationResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        $allColumns   = ApplicationsExport::availableColumns();  // ['key' => 'Label', ...]
        $columnKeys   = array_keys($allColumns);
        $columnLabels = array_values($allColumns);

        // Build checkbox list options: ['key' => 'Label', ...]
        $options = $allColumns;

        return [
            Actions\Action::make('export')
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                // ── Column-picker modal ──────────────────────────────────────
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Application Status')
                        ->options([
                            'submitted' => 'Submitted',
                            'draft'     => 'Draft',
                        ])
                        ->placeholder('Select a status')
                        ->required()
                        ->validationMessages([
                            'required' => 'Please select a status to export.',
                        ]),
                    Forms\Components\DatePicker::make('date_from')
                        ->label('Submitted From')
                        ->nullable()
                        ->displayFormat('d/m/Y')
                        ->native(false),
                    Forms\Components\DatePicker::make('date_to')
                        ->label('Submitted To')
                        ->nullable()
                        ->displayFormat('d/m/Y')
                        ->native(false),
                    Forms\Components\CheckboxList::make('columns')
                        ->label('Select columns to export')
                        ->options($options)
                        ->default($columnKeys)          // all ticked by default
                        ->columns(3)
                        ->selectAllAction(
                            fn (Forms\Components\Actions\Action $action) => $action->label('Select all')
                        )
                        ->deselectAllAction(
                            fn (Forms\Components\Actions\Action $action) => $action->label('Deselect all')
                        )
                        ->bulkToggleable()              // renders the Select all / Deselect all links
                        ->required()
                        ->minItems(1)
                        ->validationMessages([
                            'min_items' => 'Please select at least one column.',
                        ]),
                ])
                ->modalHeading('Choose columns to export')
                ->modalSubmitActionLabel('Export')
                ->modalWidth('4xl')
                // ── Download on submit ───────────────────────────────────────
                ->action(function (array $data) {
                    $selected = $data['columns'] ?? array_keys(ApplicationsExport::availableColumns());
                    $status   = $data['status'];
                    $dateFrom = $data['date_from'] ?? null;
                    $dateTo   = $data['date_to']   ?? null;

                    return Excel::download(
                        new ApplicationsExport($selected, $status, null, $dateFrom, $dateTo),
                        'applications_' . $status . '_' . now()->format('Y-m-d_His') . '.xlsx'
                    );
                }),

            Actions\CreateAction::make(),
        ];
    }
}
