<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\UrlRewrite\Model\Version191to2000;

use Migration\ResourceModel\Record;

/**
 * Class Transformer
 */
class Transformer
{
    /**
     * @var array
     */
    protected $redirectTypesMapping = [
        '' => 0,
        'R' => 302,
        'RP' => 301
    ];

    /**
     * Record transformer
     *
     * @param Record $record
     * @param Record $destRecord
     * @return void
     */
    public function transform(Record $record, Record $destRecord)
    {
        $destRecord->setValue('url_rewrite_id', $record->getValue('url_rewrite_id'));
        $destRecord->setValue('store_id', $record->getValue('store_id'));
        $destRecord->setValue('description', $record->getValue('description'));

        $destRecord->setValue('request_path', $record->getValue('request_path'));
        $destRecord->setValue('target_path', $record->getValue('target_path'));
        $destRecord->setValue('is_autogenerated', $record->getValue('is_system'));

        $destRecord->setValue('entity_type', $this->getRecordEntityType($record));

        $metadata = $this->doRecordSerialization($record)
            ? json_encode(['category_id' => $record->getValue('category_id')])
            : null ;
        $destRecord->setValue('metadata', $metadata);

        $destRecord->setValue('entity_id', $record->getValue('product_id') ?: $record->getValue('category_id'));
        $redirectType = isset($this->redirectTypesMapping[$record->getValue('options')])
            ? $this->redirectTypesMapping[$record->getValue('options')]
            : $this->redirectTypesMapping[''];
        $destRecord->setValue('redirect_type', $redirectType);
    }

    /**
     * Do record serialization
     *
     * @param Record $record
     * @return bool
     */
    private function doRecordSerialization(Record $record)
    {
        return $record->getValue('is_system') && $record->getValue('product_id') && $record->getValue('category_id');
    }

    /**
     * Get record entity type
     *
     * @param Record $record
     * @return mixed
     */
    private function getRecordEntityType(Record $record)
    {
        $isCategory = $record->getValue('category_id') ? 'category' : null;
        $isProduct = $record->getValue('product_id') ? 'product' : null;
        return $isProduct ?: $isCategory;
    }
}