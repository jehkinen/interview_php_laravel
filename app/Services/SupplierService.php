<?php


namespace App\Services;


use App\Dto\SupplierDto;
use App\Models\Supplier;
use App\Traits\ValidationErrorTrait;

class SupplierService
{
    use ValidationErrorTrait;

    /**
     * @param SupplierDto $dto
     * @return Supplier
     */
    public function store(SupplierDto $dto)
    {
        /**
         * I would not like to move complex validation with database checking to Requests layer like name -> unique:suppliers,
         * because request should contain some basics validation like length or format, any db checks should be in service layer
         */
        $supplierExists = Supplier::whereName($dto->getName())->exists();

        if ($supplierExists) {
            $this->throwClientError('name', 'Supplier with such name is already exists');
        }

        /** @var Supplier $supplier */
        $supplier = new Supplier();
        $supplier->name = $dto->getName();
        $supplier->url = $dto->getUrl() ?? '';
        $supplier->info = $dto->getInfo();
        $supplier->rules = $dto->getRules();
        $supplier->district = $dto->getDistinct();
        $supplier->save();

        return $supplier;
    }
}
