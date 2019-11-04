<?php

namespace DomainLayer\ORM\BulkUploadRequest;

use DomainLayer\ORM\DomainEntity\DomainEntity;

/**
 * Class BulkUploadRequest
 * @package DomainLayer\ORM\BulkUploadRequest
 */
class BulkUploadRequest extends DomainEntity
{

	const STATUS_PENDING = 'pending';
	const STATUS_APPROVED = 'approved';
	const STATUS_REJECTED = 'rejected';

	/**
	 * @var string
	 */
	protected $status;

    /** name
     *
     *
     *
     * @var string
     */
    private $name;

    /** $emailAddress
     *
     *  The email address of the contributor.
     *
     * @var string
     */
    private $emailAddress;

    /** organisation
     *
     *
     *
     * @var string|null
     */
    private $organisation;

    /** description
     *
     *
     *
     * @var string|null
     */
    private $description;

    /** approvedAt
     *
     * The timestamp the bulk upload request was approved.
     *
     * @var \DateTime|null
     */
    private $approvedAt;

    /** approvalToken
     *
     *
     *
     * @var string
     */
    private $approvalToken;

    /** approvalToken
     *
     *
     *
     * @var string|null
     */
    private $exchangeToken;

    /** __construct
     *
     *  Constructor
     *
     * @param string $name
     * @param string $emailAddress
     * @param null|string $organisation
     * @param null|string $description
     * @param string $approvalToken
     */
    public function __construct($name, $emailAddress, $organisation, $description, $approvalToken) {
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->organisation = $organisation;
        $this->description = $description;
        $this->approvalToken = $approvalToken;
        $this->status = self::STATUS_PENDING;
    }

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

    /** Name
     *
     *  Returns the
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /** EmailAddress
     *
     *  Returns the
     *
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /** Organisation
     *
     *  Returns the
     *
     * @return string
     */
    public function getOrganisation() {
        return $this->organisation;
    }

    /** ExchangeToken
     *
     *  Returns the
     *
     * @return null|string
     */
    public function getExchangeToken() {
        return $this->exchangeToken;
    }

    /** Description
     *
     *  Returns the
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /** ApprovedAt
     *
     *  Returns the
     *
     * @return \DateTime|null
     */
    public function getApprovedAt() {
        return $this->approvedAt;
    }

    /** ApprovalToken
     *
     *  Returns the
     *
     * @return string
     */
    public function getApprovalToken() {
        return $this->approvalToken;
    }

    /** ApprovedAt
     *
     *  Sets the
     *
     * @param \DateTime|null $approvedAt
     */
    public function setApprovedAt($approvedAt) {
        $this->approvedAt = $approvedAt;
    }

    /** ExchangeToken
     *
     *  Sets the
     *
     * @param null|string $exchangeToken
     */
    public function setExchangeToken($exchangeToken) {
        $this->exchangeToken = $exchangeToken;
    }
}
