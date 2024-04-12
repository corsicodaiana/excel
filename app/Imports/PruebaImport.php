<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PruebaImport implements ToCollection, WithHeadingRow
{
    private function rules(): array
    {
        return [
            '*.dni' => 'required|numeric'
        ];
    }

    private function customValidationMessages()
    {
        return [
            '*.dni.required' => 'Falta el DNI en la fila :attribute',
            '*.dni.numeric' => 'El DNI en la fila :attribute debe ser numérico',
        ];
    }

    public function collection(Collection $rows)
    {
        // Reordenar los índices para que coincidan con las filas del Excel, en caso de tener que indicar errores
        $nuevoIndiceInicio = 2;
        // Creamos un array con los nuevos índices
        $indices = range($nuevoIndiceInicio, $nuevoIndiceInicio + count($rows) - 1);
        // Creamos un nuevo array combinando los datos con los nuevos índices
        $rows = array_combine($indices, $rows->toArray());

        // Eliminar el encabezado del Excel
        unset($rows[2]);

        // Convertir fechas que vienen del Excel
        foreach ($rows as &$row) {  // & para que modifique el array $rows, sino modifica la copia
            $row['fecha_nacimiento'] = Date::excelToDateTimeObject($row['fecha_nacimiento'])->format('Y-m-d');
        }

        $validator = Validator::make(
            $rows,
            $this->rules(),
            $this->customValidationMessages()
        );
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->all());
        }
    }
}
