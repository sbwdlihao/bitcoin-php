<?php

namespace BitWasp\Bitcoin\Block;

use BitWasp\Bitcoin\Math\Math;
use BitWasp\Bitcoin\Collection\Transaction\TransactionCollection;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\Buffertools;
use BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty;
use Pleo\Merkle\FixedSizeTree;

class MerkleRoot
{
    /**
     * @var TransactionCollection
     */
    private $transactions;

    /**
     * @var Math
     */
    private $math;

    /**
     * @var Buffer
     */
    private $lastHash;

    /**
     * Instantiate the class when given a block
     *
     * @param Math $math
     * @param TransactionCollection $txCollection
     */
    public function __construct(Math $math, TransactionCollection $txCollection)
    {
        $this->math = $math;
        $this->transactions = $txCollection;
    }

    /**
     * @return Buffer
     */
    private function getLastHash()
    {
        return $this->lastHash;
    }

    /**
     * Set the last hash. Should only be set by calculateHash()
     *
     * @param Buffer $lastHash
     */
    private function setLastHash(Buffer $lastHash)
    {
        $this->lastHash = $lastHash;
    }

    /**
     * @param callable|null $hashFunction
     * @return Buffer
     * @throws MerkleTreeEmpty
     */
    public function calculateHash(callable $hashFunction = null)
    {
        $hashFxn = $hashFunction ?: function ($value) {
            return hash('sha256', hash('sha256', $value, true), true);
        };

        $txCount = count($this->transactions);

        if ($txCount === 0) {
            // TODO: Probably necessary. Should always have a coinbase at least.
            throw new MerkleTreeEmpty('Cannot compute Merkle root of an empty tree');
        }

        if ($txCount === 1) {
            $binary = $hashFxn($this->transactions[0]->getBinary());

        } else {
            // Create a fixed size Merkle Tree
            $tree = new FixedSizeTree($txCount + ($txCount % 2), $hashFxn);

            // Compute hash of each transaction
            $last = '';
            foreach ($this->transactions as $i => $transaction) {
                $last = $transaction->getBinary();
                $tree->set($i, $last);
            }

            // Check if we need to repeat the last hash (odd number of transactions)
            if (!$this->math->isEven($txCount)) {
                $tree->set($txCount, $last);
            }

            $binary = $tree->hash();
        }

        $this->setLastHash((new Buffer($binary))->flip());
        return $this->getLastHash();
    }
}
