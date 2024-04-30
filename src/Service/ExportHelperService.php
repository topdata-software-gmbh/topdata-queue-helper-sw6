<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskDefinition;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskEntity;
use Topdata\TopdataQueueHelperSW6\Util\UtilDebug;

/**
 * 04/2024 created
 */
class ExportHelperService
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    )
    {
        $this->connection = $connection;
    }

    public function getExports(bool $bAll): array
    {
        $SQL = "SELECT  
                            sc.active,
                            sct.name,
                            LOWER(HEX(pe.id)) AS product_export_id,
                            LOWER(HEX(sc.id)) AS sales_channel_id, 
                            pe.file_name, 
                            pe.generate_by_cronjob, 
                            pe.interval
                FROM sales_channel sc
                JOIN sales_channel_translation sct ON sc.id = sct.sales_channel_id AND sct.language_id = UNHEX(:languageId)
                JOIN product_export pe ON sc.id = pe.sales_channel_id
                
                WHERE sc.type_id = UNHEX(:typeId) AND sc.active IN(:active)";


        $params = [
            'typeId'     => Defaults::SALES_CHANNEL_TYPE_PRODUCT_COMPARISON,
            'languageId' => Defaults::LANGUAGE_SYSTEM,
        ];
        if($bAll) {
            $params['active'] = [0, 1];
        } else {
            $params['active'] = [1];
        }
        $rows = $this->connection->executeQuery($SQL, $params, ['active' => Connection::PARAM_INT_ARRAY])->fetchAllAssociative();

        return $rows;
    }
}