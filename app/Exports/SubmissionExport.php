<?php

namespace App\Exports;

use App\Models\OvertimeAssignment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubmissionExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        if ($this->query) {
            return $this->query->with(['projectuser.user.projects', 'projectuser.project']);
        }

        // Fallback query if none provided
        return OvertimeAssignment::query()->with(['projectuser.user.projects', 'projectuser.project']);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Pengajuan',
            'Nama User',
            'NIP',
            'Nama Projek',
            
            'Tanggal Pengajuan',
            'Jam Mulai',
            'Jam Selesai',
            'Tipe Pengajuan',
            'Status',
            'Deskripsi',
            'Link Foto Bukti',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
        ];
    }

    /**
     * @param mixed $overtime
     * @return array
     */
    public function map($overtime): array
    {
        // Get the first projectuser relationship
        $projectUser = $overtime->projectuser->first();
        $user = $projectUser?->user;
        $project = $projectUser?->project;
        
        // Decode JSON description
        $description = json_decode($overtime->description, true) ?? [];
        $submissionType = $description['submission_type'] ?? 'N/A';
        $evidence = $description['evidence'] ?? null;
        $descriptionText = $description['description'] ?? 'N/A';
        
        // Get project code - try from direct project relationship first, then from user projects
        $projectCode = $project?->code_project ?? $user?->projects?->first()?->code_project ?? 'N/A';

        return [
            $overtime->name ?? 'N/A',
            $user?->name ?? 'N/A',
            $user?->nip ?? 'N/A',
            $project?->name ?? $user?->projects?->first()?->name ?? 'N/A',
            $overtime->submission_date ? date('Y-m-d', strtotime($overtime->submission_date)) : 'N/A',
            $overtime->start_time ? date('H:i', strtotime($overtime->start_time)) : 'N/A',
            $overtime->end_time ? date('H:i', strtotime($overtime->end_time)) : 'N/A',
            $submissionType,
            ucfirst($overtime->status ?? 'N/A'),
            $descriptionText,
            $evidence ? asset('storage/' . $evidence) : 'Tidak ada foto',
            $overtime->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $overtime->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set column widths
            'A' => ['width' => 20],
            'B' => ['width' => 25],
            'C' => ['width' => 15],
            'D' => ['width' => 25],
            'E' => ['width' => 15],
            'F' => ['width' => 15],
            'G' => ['width' => 12],
            'H' => ['width' => 12],
            'I' => ['width' => 20],
            'J' => ['width' => 12],
            'K' => ['width' => 30],
            'L' => ['width' => 40],
            'M' => ['width' => 18],
            'N' => ['width' => 18],
        ];
    }
}