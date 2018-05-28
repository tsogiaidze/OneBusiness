<?php
namespace App\Transformers\WageTmpl8;

use League\Fractal;
use App\Models\WageTmpl8\Mstr;

class MstrTransformer extends Fractal\TransformerAbstract
{
    public function transform(Mstr $item)
    {
        return [
            'department' => $item->department ? $item->department->department : '',
            'wage_tmpl8_id' => (int) $item->wage_tmpl8_id,
            'code' => $item->code,
            'position' => $item->position,
            'active' => (int) $item->active,
            'total' => number_format("0", 2)
        ];
    }
}