<?php

namespace App\Http\Resources;

/**
 * Class User.
 * @mixin \App\Models\User
 */
class UserResource extends AbstractResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'two_fa_enabled' => $this->two_factor_enabled,

            'roles' => $this
                ->roles()
                ->where('guard_name', 'api')
                ->pluck( 'name')
                ->all(),
        ];
    }

}
