<?php

declare(strict_types=1);

namespace CarlLee\EcPayB2B\Operations;

use CarlLee\EcPayB2B\Content;
use CarlLee\EcPayB2B\Parameter\CustomerType;
use CarlLee\EcPayB2B\Parameter\ExchangeMode;
use CarlLee\EcPayB2B\Parameter\MaintainAction;
use Exception;

/**
 * 交易對象維護 API。
 *
 * B2B 電子發票分為交換與存證模式，在串接此規格文件前，
 * 必須先用此 API 設定交易對象(買方/賣方/買賣方)、設定開立形式(交換/存證)
 * 以及新增交易對象的相關資訊。
 *
 * 注意：同一個統編下若有多個 MerchantID，這些 MerchantID 僅能以「存證」開立方式
 * 來建立相同交易對象，即這些 MerchantID 在新增相同交易對象時參數 ExchangeMode
 * 都只能設為 0:存證。
 *
 * @see https://developers.ecpay.com.tw/?p=14830
 */
class MaintainMerchantCustomerData extends Content
{
    /**
     * API endpoint path.
     *
     * @var string
     */
    protected string $requestPath = '/B2BInvoice/MaintainMerchantCustomerData';

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
            'Action' => '',
            'CustomerNumber' => '',
            'Identifier' => '',
            'type' => '',
            'CompanyName' => '',
            'Address' => '',
            'TelephoneNumber' => '',
            'TradingSlang' => '',
            'ExchangeMode' => '',
            'EmailAddress' => '',
            'SalesName' => '',
            'ContactAddress' => '',
        ];
    }

    /**
     * 設定動作類型。
     *
     * @param string $action Add/Update/Delete
     * @return self
     */
    public function setAction(string $action): self
    {
        if (!MaintainAction::isValid($action)) {
            throw new Exception('Action must be Add, Update, or Delete.');
        }

        $this->content['Data']['Action'] = $action;

        return $this;
    }

    /**
     * 設定為新增動作。
     *
     * @return self
     */
    public function add(): self
    {
        return $this->setAction(MaintainAction::ADD);
    }

    /**
     * 設定為編輯動作。
     *
     * @return self
     */
    public function update(): self
    {
        return $this->setAction(MaintainAction::UPDATE);
    }

    /**
     * 設定為刪除動作。
     *
     * @return self
     */
    public function delete(): self
    {
        return $this->setAction(MaintainAction::DELETE);
    }

    /**
     * 設定公司編號。
     *
     * 可以與統一編號相同。
     *
     * @param string $customerNumber
     * @return self
     */
    public function setCustomerNumber(string $customerNumber): self
    {
        $customerNumber = trim($customerNumber);

        if (strlen($customerNumber) > 20) {
            throw new Exception('CustomerNumber cannot exceed 20 characters.');
        }

        $this->content['Data']['CustomerNumber'] = $customerNumber;

        return $this;
    }

    /**
     * 設定統一編號。
     *
     * 固定長度為數字 8 碼、註冊當下所使用的統一編號、設定後不可變更。
     *
     * @param string $identifier
     * @return self
     */
    public function setIdentifier(string $identifier): self
    {
        $identifier = trim($identifier);

        if (!preg_match('/^\d{8}$/', $identifier)) {
            throw new Exception('Identifier must be exactly 8 digits.');
        }

        $this->content['Data']['Identifier'] = $identifier;

        return $this;
    }

    /**
     * 設定交易對象類型。
     *
     * @param string $type 1: 買方, 2: 賣方, 3: 買賣方
     * @return self
     */
    public function setType(string $type): self
    {
        if (!CustomerType::isValid($type)) {
            throw new Exception('type must be 1 (買方), 2 (賣方), or 3 (買賣方).');
        }

        $this->content['Data']['type'] = $type;

        return $this;
    }

    /**
     * 設定交易對象為買方。
     *
     * @return self
     */
    public function asBuyer(): self
    {
        return $this->setType(CustomerType::BUYER);
    }

    /**
     * 設定交易對象為賣方。
     *
     * @return self
     */
    public function asSeller(): self
    {
        return $this->setType(CustomerType::SELLER);
    }

    /**
     * 設定交易對象為買賣方。
     *
     * @return self
     */
    public function asBoth(): self
    {
        return $this->setType(CustomerType::BOTH);
    }

    /**
     * 設定公司名稱。
     *
     * @param string $companyName
     * @return self
     */
    public function setCompanyName(string $companyName): self
    {
        $companyName = trim($companyName);

        $this->content['Data']['CompanyName'] = $companyName;

        return $this;
    }

    /**
     * 設定公司地址。
     *
     * @param string $address
     * @return self
     */
    public function setAddress(string $address): self
    {
        $address = trim($address);

        $this->content['Data']['Address'] = $address;

        return $this;
    }

    /**
     * 設定公司電話。
     *
     * @param string $telephoneNumber
     * @return self
     */
    public function setTelephoneNumber(string $telephoneNumber): self
    {
        $telephoneNumber = trim($telephoneNumber);

        $this->content['Data']['TelephoneNumber'] = $telephoneNumber;

        return $this;
    }

    /**
     * 設定交易代號。
     *
     * @param string $tradingSlang
     * @return self
     */
    public function setTradingSlang(string $tradingSlang): self
    {
        $tradingSlang = trim($tradingSlang);

        $this->content['Data']['TradingSlang'] = $tradingSlang;

        return $this;
    }

    /**
     * 設定開立形式。
     *
     * @param string $mode 0: 存證, 1: 交換
     * @return self
     */
    public function setExchangeMode(string $mode): self
    {
        if (!ExchangeMode::isValid($mode)) {
            throw new Exception('ExchangeMode must be 0 (存證) or 1 (交換).');
        }

        $this->content['Data']['ExchangeMode'] = $mode;

        return $this;
    }

    /**
     * 設定開立形式為存證模式。
     *
     * 綠界僅會將發票資料上傳至財政部，僅適用於銷項發票。
     * 加值中心無法接收其他營業人開立給您的電子發票。
     *
     * @return self
     */
    public function archiveMode(): self
    {
        return $this->setExchangeMode(ExchangeMode::ARCHIVE);
    }

    /**
     * 設定開立形式為交換模式。
     *
     * 綠界會將發票資料上傳至財政部發票傳輸軟體供對方營業人確認及接收。
     * 請務必先至財政部平台設定由綠界接收。
     *
     * @return self
     */
    public function exchangeMode(): self
    {
        return $this->setExchangeMode(ExchangeMode::EXCHANGE);
    }

    /**
     * 設定公司信箱。
     *
     * 可輸入多組，以半形分號區隔。
     *
     * @param string|array $emails 單一信箱或信箱陣列
     * @return self
     */
    public function setEmailAddress(string|array $emails): self
    {
        if (is_array($emails)) {
            $emails = implode(';', $emails);
        }

        $emails = trim($emails);

        if (strlen($emails) > 200) {
            throw new Exception('EmailAddress cannot exceed 200 characters.');
        }

        // 驗證每個信箱格式
        $emailList = explode(';', $emails);
        foreach ($emailList as $email) {
            $email = trim($email);
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format: ' . $email);
            }
        }

        $this->content['Data']['EmailAddress'] = $emails;

        return $this;
    }

    /**
     * 設定業務負責人。
     *
     * @param string $salesName
     * @return self
     */
    public function setSalesName(string $salesName): self
    {
        $salesName = trim($salesName);

        if (strlen($salesName) > 20) {
            throw new Exception('SalesName cannot exceed 20 characters.');
        }

        $this->content['Data']['SalesName'] = $salesName;

        return $this;
    }

    /**
     * 設定聯絡地址。
     *
     * @param string $contactAddress
     * @return self
     */
    public function setContactAddress(string $contactAddress): self
    {
        $contactAddress = trim($contactAddress);

        if (strlen($contactAddress) > 100) {
            throw new Exception('ContactAddress cannot exceed 100 characters.');
        }

        $this->content['Data']['ContactAddress'] = $contactAddress;

        return $this;
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

        // 驗證動作
        if (empty($this->content['Data']['Action'])) {
            throw new Exception('Action cannot be empty.');
        }

        if (!MaintainAction::isValid($this->content['Data']['Action'])) {
            throw new Exception('Action must be Add, Update, or Delete.');
        }

        // 驗證統一編號
        if (empty($this->content['Data']['Identifier'])) {
            throw new Exception('Identifier cannot be empty.');
        }

        if (!preg_match('/^\d{8}$/', $this->content['Data']['Identifier'])) {
            throw new Exception('Identifier must be exactly 8 digits.');
        }

        // 驗證交易對象類型
        if (empty($this->content['Data']['type'])) {
            throw new Exception('type cannot be empty.');
        }

        if (!CustomerType::isValid($this->content['Data']['type'])) {
            throw new Exception('type must be 1 (買方), 2 (賣方), or 3 (買賣方).');
        }

        // 驗證公司信箱（必填）
        if (empty($this->content['Data']['EmailAddress'])) {
            throw new Exception('EmailAddress cannot be empty.');
        }

        // 驗證開立形式（新增時必填）
        if (
            $this->content['Data']['Action'] === MaintainAction::ADD &&
            $this->content['Data']['ExchangeMode'] === ''
        ) {
            throw new Exception('ExchangeMode cannot be empty when adding.');
        }

        if (
            $this->content['Data']['ExchangeMode'] !== '' &&
            !ExchangeMode::isValid($this->content['Data']['ExchangeMode'])
        ) {
            throw new Exception('ExchangeMode must be 0 (存證) or 1 (交換).');
        }
    }
}
