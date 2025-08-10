<?php

    namespace App\Imports;

    use App\Models\Placement;
    use App\Models\Project;
    use App\Models\ProjectManpower;
    use App\Models\RelationPlacementProject;
    use App\Models\RelationPlacementUser;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;
    use Maatwebsite\Excel\Concerns\ToModel; 
    use Maatwebsite\Excel\Concerns\WithHeadingRow;

    class ManpowerImport implements ToModel, WithHeadingRow
    {
        public function model(array $row)
        {
            // Step 1: More profile data
            $more_profile_data = [
                'group' => $row['profesi'] ?? null,
                'notelp' => $row['no_telp'] ?? null,
                'gender' => $row['jenis_kelamin'] ?? null,
            ];

            // Step 2: Create new user (manpower)
            $manpower = new User([
                'name' => $row['nama'] , // This seems suspicious - double check this
                'nip' => $row['nip'] ?? null,
                'email' => $row['email'] ?? null,
                'description' => json_encode($more_profile_data),
                'password' => bcrypt($row['password']),
            ]);

            
            $manpower->save();
            $manpower->assignRole('man_power');    
            // Step 3: Get placement (based on kode_placement)
            $placement = Placement::with('project')
        ->where('kode_placement', $row['kode_placement'] ?? null)
        ->first();

        
            
            $project_id = $placement->project->project_id;
            
            // Step 4: Create relation if placement found
            if ($placement) {
                RelationPlacementUser::create([
                    'placement_id' => $placement->id,
                    'user_id' => $manpower->id,
                ]);
            }

            if ($project_id) {
                ProjectManpower::create([
                    'project_id' => $project_id,
                    'user_id' => $manpower->id,
                ]);
            }

            

            

            return $manpower;
        }
    }
