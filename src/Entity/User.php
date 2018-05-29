<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username", message="Ce pseudo est déjà pris")
 * @UniqueEntity("email", message="Un compte existe déjà avec cet email")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex("/^\w+$/")
     */
    private $username;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Length(max=4096)
	 */
	private $plainPassword;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=120, unique=true)
     * @Assert\NotBlank())
     * @Assert\Email()
     */
    private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\Column(type="array")
	 */
	private $roles;

   /**
   * @ORM\OneToMany(targetEntity="App\Entity\Quizz", mappedBy="User")
   */
    private $quizz;

	public function __construct()
	{
		$this->isActive = true;
		$this->roles = array('ROLE_USER');
		$this->quizz = new ArrayCollection();
	}

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword( $plainPassword )
	{
		$this->plainPassword = $plainPassword;
	}

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

	public function serialize()
	{
		return serialize(array(
			$this->id,
			$this->username,
			$this->password
		));
	}

	public function unserialize( $serialized )
	{
		list (
			$this->id,
			$this->username,
			$this->password
			) = unserialize($serialized, ['allowed_classes' => false]);
	}

	public function getRoles()
	{
		return array('ROLE_USER');
	}

	public function getSalt()
	{
		return null;
	}

	public function eraseCredentials()
	{
		// TODO: Implement eraseCredentials() method.
	}

	 /**
	  * @return Collection|Quizz[]
	  */
	 public function getQuizz(): Collection
	 {
	     return $this->quizz;
	 }

	 public function addQuizz(Quizz $quizz): self
	 {
	     if (!$this->quizz->contains($quizz)) {
	         $this->quizz[] = $quizz;
	         $quizz->setUser($this);
	     }
	     return $this;
	 }

	 public function removeQuizz(Quizz $quizz): self
	 {
	     if ($this->quizz->contains($quizz)) {
	         $this->quizz->removeElement($quizz);
	         // set the owning side to null (unless already changed)
	         if ($quizz->getUser() === $this) {
	             $quizz->setUser(null);
	         }
	     }
	     return $this;
	 }

}
