<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\InvoiceWordStatus;
use Exception;

/**
 * 設定字軌號碼狀態 API。
 *
 * 營業人(特店)新增字軌後，字軌的預設狀態皆為已審核且未啟用。
 * 如欲使用字軌，必須先設定狀態將字軌啟用。
 * 在開立發票之前，必須先將已新增完成的字軌做狀態的設定。
 *
 * @see https://developers.ecpay.com.tw/?p=14840
 */
class UpdateInvoiceWordStatus extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/UpdateInvoiceWordStatus';

    /**
     * Initialize request payload.
     *
     * @return void
     */
    #[\Override]
    protected function initContent(): void
    {
        $this->content['Data'] = [
            'MerchantID' => $this->merchantID,
            'TrackID' => '',
            'InvoiceStatus' => InvoiceWordStatus::ENABLED,
        ];
    }

    /**
     * 設定字軌號碼 ID。
     *
     * 為新增字軌後取到的 TrackID。
     *
     * @param string $trackId
     * @return self
     */
    public function setTrackID(string $trackId): self
    {
        $trackId = trim($trackId);

        if ($trackId === '') {
            throw new Exception('TrackID cannot be empty.');
        }

        if (strlen($trackId) > 10) {
            throw new Exception('TrackID cannot exceed 10 characters.');
        }

        $this->content['Data']['TrackID'] = $trackId;

        return $this;
    }

    /**
     * 設定發票字軌狀態。
     *
     * @param int $status 0: 停用, 1: 暫停, 2: 啟用
     * @return self
     */
    public function setInvoiceStatus(int $status): self
    {
        $this->assertInvoiceStatus($status);
        $this->content['Data']['InvoiceStatus'] = $status;

        return $this;
    }

    /**
     * 將字軌設定為停用狀態。
     *
     * 注意：如狀態設定為停用，該字軌區間無法上傳發票。
     *
     * @return self
     */
    public function disable(): self
    {
        return $this->setInvoiceStatus(InvoiceWordStatus::DISABLED);
    }

    /**
     * 將字軌設定為暫停狀態。
     *
     * @return self
     */
    public function suspend(): self
    {
        return $this->setInvoiceStatus(InvoiceWordStatus::SUSPENDED);
    }

    /**
     * 將字軌設定為啟用狀態。
     *
     * @return self
     */
    public function enable(): self
    {
        return $this->setInvoiceStatus(InvoiceWordStatus::ENABLED);
    }

    /**
     * 驗證 payload。
     *
     * @return void
     */
    #[\Override]
    protected function validation(): void
    {
        $this->validatorBaseParam();

        if (empty($this->content['Data']['TrackID'])) {
            throw new Exception('TrackID cannot be empty.');
        }

        $this->assertInvoiceStatus($this->content['Data']['InvoiceStatus']);
    }

    /**
     * 確保發票字軌狀態在允許範圍內。
     *
     * @param int $status
     * @return void
     */
    private function assertInvoiceStatus(int $status): void
    {
        if (!InvoiceWordStatus::isValid($status)) {
            throw new Exception('InvoiceStatus must be 0 (停用), 1 (暫停), or 2 (啟用).');
        }
    }
}
