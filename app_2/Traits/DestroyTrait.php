<?php

namespace App\Traits;

use App\Http\Controllers\Api\Controller;

/**
 * Trait DestroyTrait.
 * @mixin Controller
 */
trait DestroyTrait
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model::findOrFail($id);
        //$this->authorize('delete', $model);
        $model->delete();

        return response(null, 204);
    }
}
