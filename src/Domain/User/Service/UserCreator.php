<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserCreatorRepository;
use App\Exception\ValidationException;
use Slim\Logger;

/**
 * Service.
 */
final class UserCreator
{
    /**
     * @var UserCreatorRepository
     */
    private $repository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * The constructor.
     *
     * @param UserCreatorRepository $repository The repository
     * @param Logger                $loger      The logger
     */
    public function __construct(UserCreatorRepository $repository, Logger $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Create a new user.
     *
     * @param array $data The form data
     *
     * @return int The new user ID
     */
    public function createUser(array $data): int
    {
        // Input validation
        $this->validateNewUser($data);

        // Insert user
        $userId = $this->repository->insertUser($data);

        // Logging here: User created successfully
        $this->logger->info(sprintf('user %s created with id: %s', $data['username'], $userId));

        return $userId;
    }

    /**
     * Input validation.
     *
     * @param array $data The form data
     *
     * @throws ValidationException
     */
    private function validateNewUser(array $data): void
    {
        $errors = [];

        // Here you can also use your preferred validation library

        if (empty($data['username']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['profile'])) {
            $errors['mandatory'] = 'Input [User  Name] [Password] [Firstname] [Lastname] [Email] [Profile] required';
        }

        if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }

        if (!empty($errors)) {
            throw new ValidationException('Please check your input.', $errors);
        }

        if ($this->repository->userExists($data['username'], $data['email'])) {
            throw new ValidationException('User name already exists with name '.$data['username'].' or email '.$data['email'].'.', $errors);
        }
    }
}
