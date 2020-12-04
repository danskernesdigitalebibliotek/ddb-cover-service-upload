<?php

/**
 * @file
 * The cover upload messages class.
 *
 * Please note that this class is shared between this repository and the cover upload service repository.
 */

namespace App\Message;

/**
 * Class CoverUploadMessage.
 */
class CoverUserUploadMessage
{
    private $operation;
    private $identifierType;
    private $identifier;
    private $imageUrl;
    private $accrediting;
    private $vendorId;
    private $requestId;

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     *
     * @return CoverUserUploadMessage
     */
    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifierType(): string
    {
        return $this->identifierType;
    }

    /**
     * @param string $type
     *
     * @return CoverUserUploadMessage
     */
    public function setIdentifierType(string $type): self
    {
        $this->identifierType = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return CoverUserUploadMessage
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     *
     * @return CoverUserUploadMessage
     */
    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @param string $accrediting
     *
     * @return CoverUserUploadMessage
     */
    public function setAccrediting(string $accrediting): self
    {
        $this->accrediting = $accrediting;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccrediting(): string
    {
        return $this->accrediting;
    }

    /**
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * @param mixed $vendorId
     *
     * @return CoverUserUploadMessage
     */
    public function setVendorId($vendorId): self
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    /**
     * Get request id (which is unique for the whole request).
     *
     * @return string
     *   The request id
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set request id (which is unique for the whole request).
     *
     * @param string $requestId
     *   The request id (normally found in HTTP_X_REQUEST_ID)
     *
     * @return CoverUserUploadMessage
     */
    public function setRequestId(string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }
}
