<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * リソースを配列に変換
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        // TodoモデルをJSONレスポンス用の配列に変換するよ〜
        return [
            'id' => $this->id,
            'title' => $this->title,
            'completed' => (bool) $this->completed,
            'completed_at' => $this->completed_at,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}