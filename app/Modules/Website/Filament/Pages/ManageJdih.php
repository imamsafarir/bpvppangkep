<?php

namespace App\Modules\Website\Filament\Pages;

use App\Modules\Website\Models\Jdih;
use BackedEnum;
use Illuminate\Support\Str;

/* ===========================
| FILAMENT CORE
=========================== */
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/* ===========================
| SCHEMA & FORM
=========================== */
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;

/* ===========================
| FORM COMPONENTS
=========================== */
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

/* ===========================
| TABLE & WIDGET
=========================== */
use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

/**
 * =========================================================
 * PAGE: MANAGE JDIH
 * =========================================================
 */
class ManageJdih extends Page
{
    protected string $view = 'website::filament.manage-jdih';
    protected string|\Filament\Support\Enums\Width|null $maxContentWidth = 'full';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-scale';
    protected static ?string $title = 'Kelola JDIH';
    protected static ?string $navigationLabel = 'JDIH / Produk Hukum';


    public static function getNavigationGroup(): ?string
    {
        return 'Website';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isWebsite() || auth()->user()?->isStaff();
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    TextInput::make('nomor_peraturan')
                        ->label('Nomor / Seri Peraturan')
                        ->placeholder('KEP/14/BPVP-PKP/VI/2026')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('judul_peraturan')
                        ->label('Judul Peraturan / Regulasi')
                        ->required()
                        ->maxLength(255),

                    Select::make('status_peraturan')
                        ->label('Status Pemberlakuan')
                        ->options([
                            'berlaku' => 'Masih Berlaku',
                            'tidak_berlaku' => 'Sudah Tidak Berlaku / Dicabut',
                        ])
                        ->required(),

                    Textarea::make('tentang')
                        ->label('Tentang / Ringkasan Singkat')
                        ->rows(2),

                    FileUpload::make('file_path')
                        ->label('Upload Dokumen Resmi (PDF)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->required()
                        ->maxSize(15360)
                        ->disk('public')
                        ->visibility('public')
                        ->directory('jdih/dokumen')
                        ->getUploadedFileNameForStorageUsing(function ($get, $file) {
                            return Str::slug($get('nomor_peraturan'))
                                . '-' . time()
                                . '.' . $file->getClientOriginalExtension();
                        }),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan & Publikasikan Regulasi')
                                ->submit('save')
                                ->color('primary'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Jdih::create($this->form->getState());

        $this->form->fill();
        $this->dispatch('refreshTables');

        Notification::make()
            ->success()
            ->title('Produk hukum berhasil ditambahkan ke JDIH')
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            DaftarJdihTable::class,
        ];
    }
}

/**
 * =========================================================
 * WIDGET: DAFTAR JDIH
 * =========================================================
 */
class DaftarJdihTable extends TableWidget
{
    protected static ?string $heading = '📚 Daftar Produk Hukum / Regulasi';
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refreshTables' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Jdih::query()->latest())
            ->columns([
                TextColumn::make('nomor_peraturan')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('judul_peraturan')
                    ->label('Judul')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('status_peraturan')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'berlaku' => 'success',
                        'tidak_berlaku' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'berlaku' => 'Masih Berlaku',
                        'tidak_berlaku' => 'Tidak Berlaku',
                        default => $state,
                    }),
            ])
            ->actions([
                Action::make('view_file')
                    ->label('Lihat PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->form([
                        TextInput::make('nomor_peraturan')->required(),
                        TextInput::make('judul_peraturan')->required(),
                        Select::make('status_peraturan')
                            ->options([
                                'berlaku' => 'Masih Berlaku',
                                'tidak_berlaku' => 'Sudah Tidak Berlaku / Dicabut',
                            ])
                            ->required(),
                        Textarea::make('tentang')->rows(2),
                        FileUpload::make('file_path')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->visibility('public')
                            ->directory('jdih/dokumen')
                            ->maxSize(15360),
                    ])
                    ->successNotificationTitle('Produk hukum berhasil diperbarui'),

                DeleteAction::make()
                    ->successNotificationTitle('Dokumen JDIH berhasil dihapus'),
            ]);
    }
}
