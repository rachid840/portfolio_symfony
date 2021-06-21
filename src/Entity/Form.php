<?php 

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="contact")
 */
class Form {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Vous devez remplir les champs")
     * @Assert\Length(
     *          min = 1,
     *          max = 50,
     *          minMessage = "Le champs est trop court",
     *          maxMessage = "Le champs est trop long",
     * )
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @Assert\NotBlank(message = "Vous devez remplir les champs")
     * @Assert\Length(
     *          min = 1,
     *          max = 150,
     *          minMessage = "Le champs est trop court",
     *          maxMessage = "Le champs est trop long",
     * )
     * @ORM\Column(type="string")
     */
    private $email;
    
    /**
     * @Assert\NotBlank(message = "Vous devez remplir les champs")
     * @Assert\Length(
     *          min = 1,
     *          max = 255,
     *          minMessage = "Le champs est trop court",
     *          maxMessage = "Le champs est trop long",
     * )
     * @ORM\Column(type="string")
     */
    private $content;

    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
}
?>