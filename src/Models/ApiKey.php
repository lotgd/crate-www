<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\Models;

use DateInterval;
use DateTime;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

use LotGD\Core\Models\SaveableInterface;
use LotGD\Core\Tools\Model\Saveable;
use LotGD\Core\Tools\Model\Deletor;

/**
 * Entity used to model an ApiKey.
 * @Entity
 * @Table(name="api_keys")
 */
class ApiKey implements SaveableInterface
{
    use Saveable;
    use Deletor;
    
    /** @Id @Column(type="string", length=256, unique=true); */
    private $apiKey;
    /** @OneToOne(targetEntity="User", inversedBy="apiKey", cascade={"persist"}) */
    private $user;
    /** @Column(type="datetime", name="created_at") */
    private $createdAt;
    /** @Column(type="datetime", name="expires_at") */
    private $expiresAt;
    /** @Column(type="datetime", name="last_used_at") */
    private $lastUsedAt;
    
    /**
     * Creates a new api key entry with a randomly generated key.
     * @param UserInterface $user The User to associate this apiKeys with
     * @param int $expiresIn life time of the apiKey.
     * @return \self
     */
    public static function generate(UserInterface $user, $expiresIn = 3600)
    {
        // 16*8 = 128 bit key, unique enough for our purpose (3.4e34 possibilities).
        $length = 16;
        $randomBytes = random_bytes($length);
        $apiKey = base64_encode($randomBytes);

        return new self($apiKey, $user, $expiresIn);
    }
    
    /**
     * constructs a new api key entry linking key and user.
     * @param string $apiKey
     */
    public function __construct(string $apiKey, UserInterface $user, int $expiresIn)
    {
        $this->apiKey = $apiKey;
        $this->user = $user;
        $this->createdAt = new DateTime();
        $this->lastUsedAt = new DateTime();
        $this->expiresAt = new DateTime();
        $this->expiresAt->add(DateInterval::createFromDateString(sprintf("%s seconds", $expiresIn)));
    }
    
    /**
     * Returns the api key.
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    
    /**
     * Returns true if the key is still valid.
     * @return bool
     */
    public function isValid(): bool
    {
        if (new DateTime() > $this->expiresAt) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Returns the date this apiKey was created at.
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    
    /**
     * Returns the date this apiKey expires at.
     * @return DateTime
     */
    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }
    
    /**
     * Returns the date this apiKey expires at in a string format.
     * @param string $format
     * @return string
     */
    public function getExpiresAtAsString(string $format = DateTime::W3C): string
    {
        return $this->expiresAt->format($format);
    }
    
    /**
     * Sets the date this apiKey was last used at to now.
     */
    public function setLastUsed()
    {
        $this->lastUsedAt = new DateTime();
    }
    
    /**
     * Returns the User this apiKey is associated with.
     * @return type
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
