<?php

namespace BitWasp\Bitcoin\Block;

use BitWasp\Buffertools\Buffer;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Serializable;
use BitWasp\Bitcoin\Serializer\Block\BlockHeaderSerializer;
use BitWasp\CommonTrait\FunctionAliasArrayAccess;

class BlockHeader extends Serializable implements BlockHeaderInterface
{
    use FunctionAliasArrayAccess;

    /**
     * @var int|string
     */
    private $version;

    /**
     * @var Buffer
     */
    private $prevBlock;

    /**
     * @var Buffer
     */
    private $merkleRoot;

    /**
     * @var int|string
     */
    private $timestamp;

    /**
     * @var Buffer
     */
    private $bits;

    /**
     * @var int|string
     */
    private $nonce;

    /**
     * @param int|string $version
     * @param Buffer $prevBlock
     * @param Buffer $merkleRoot
     * @param int|string $timestamp
     * @param Buffer $bits
     * @param int|string $nonce
     */
    public function __construct($version, Buffer $prevBlock, Buffer $merkleRoot, $timestamp, Buffer $bits, $nonce)
    {
        if (!is_numeric($version)) {
            throw new \InvalidArgumentException('Block header version must be numeric');
        }

        if ($prevBlock->getSize() !== 32) {
            throw new \InvalidArgumentException('Block header prevBlock must be a 32-byte Buffer');
        }

        if ($merkleRoot->getSize() !== 32) {
            throw new \InvalidArgumentException('Block header prevBlock must be a 32-byte Buffer');
        }

        if (!is_numeric($timestamp)) {
            throw new \InvalidArgumentException('Block header timestamp must be numeric');
        }

        if (!is_numeric($nonce)) {
            throw new \InvalidArgumentException('Block header nonce must be numeric');
        }

        $this->version = $version;
        $this->prevBlock = $prevBlock;
        $this->merkleRoot = $merkleRoot;
        $this->timestamp = $timestamp;
        $this->bits = $bits;
        $this->nonce = $nonce;

        $this
            ->initFunctionAlias('version', 'getVersion')
            ->initFunctionAlias('prevBlock', 'getPrevBlock')
            ->initFunctionAlias('merkleRoot', 'getMerkleRoot')
            ->initFunctionAlias('timestamp', 'getTimestamp')
            ->initFunctionAlias('bits', 'getBits')
            ->initFunctionAlias('nonce', 'getNonce');
    }

    /**
     * @return Buffer
     */
    public function getHash()
    {
        return Hash::sha256d($this->getBuffer())->flip();
    }

    /**
     * Get the version for this block
     *
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getVersion()
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getPrevBlock()
     */
    public function getPrevBlock()
    {
        return $this->prevBlock;
    }

    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getMerkleRoot()
     */
    public function getMerkleRoot()
    {
        return $this->merkleRoot;
    }

    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getBits()
     */
    public function getBits()
    {
        return $this->bits;
    }

    /**
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getNonce()
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Get the timestamp for this block
     *
     * {@inheritdoc}
     * @see \BitWasp\Bitcoin\Block\BlockHeaderInterface::getTimestamp()
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * {@inheritdoc}
     * @see \BitWasp\Buffertools\SerializableInterface::getBuffer()
     */
    public function getBuffer()
    {
        return (new BlockHeaderSerializer())->serialize($this);
    }
}
