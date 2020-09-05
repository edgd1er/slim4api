<?php

namespace App\Domain\Customer\Repository;

use App\Domain\Customer\Data\CustomerData;
use App\Factory\LoggerFactory;
use DomainException;
use PDO;

/**
 * Repository.
 */
class CustomerSearcherRepository
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    /**
     * The constructor.
     *
     * @param PDO           $connection The database connection
     * @param LoggerFactory $lf         The logger Factory
     */
    public function __construct(PDO $connection, LoggerFactory $lf)
    {
        $this->connection = $connection;
        $this->logger = $lf->addFileHandler('error.log')->addConsoleHandler()->createInstance('error');
    }

    /**
     * Get customer search
     *
     * @param string keyword Word to search
     * @param array in Field exact name/human name
     * @param mixed $keyword
     * @param mixed $in
     * @param mixed $page
     * @param mixed $pagesize
     *
     * @return customers Search of Customers
     * @throws DomainException
     *
     */
    public function getCustomers($keyword, $in, $page, $pagesize): array
    {
        // Feed the logger
        $this->logger->debug("CustomerSearcherRepository.getCustomers: page: {$page}, size: {$pagesize}");

        $customernb = $this->countCustomers();

        if (0 == $customernb) {
            throw new DomainException(sprintf('No customer!'));
        }
        $pagemax = ceil($customernb / $pagesize);
        $limit = (--$page) * $pagesize;

        if (-1 != $in) {
            $sql = "SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE {$in[1]} LIKE :keyword LIMIT :limit, :pagesize ;";
        } else {
            $sql = 'SELECT CUSID, CUSNAME, CUSADDRESS, CUSCITY, CUSPHONE, CUSEMAIL FROM customers WHERE CUSNAME LIKE :keyword OR CUSADDRESS LIKE :keyword OR CUSCITY LIKE :keyword OR CUSPHONE LIKE :keyword OR CUSEMAIL LIKE :keyword LIMIT :limit, :pagesize ;';
        }

        $statement = $this->connection->prepare($sql);

        $keyword = htmlspecialchars(strip_tags($keyword));
        $keyword = "%{$keyword}%";

        $statement->bindParam(':keyword', $keyword);
        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':pagesize', $pagesize, PDO::PARAM_INT);

        $statement->execute();

        $customers = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $customer = new CustomerData();
            $customer->id = (int)$row['CUSID'];
            $customer->name = (string)$row['CUSNAME'];
            $customer->address = (string)$row['CUSADDRESS'];
            $customer->city = (string)$row['CUSCITY'];
            $customer->phone = (string)$row['CUSPHONE'];
            $customer->email = (string)$row['CUSEMAIL'];
            array_push($customers, $customer);
        }

        if (0 == count($customers)) {
            if (-1 != $in) {
                throw new DomainException(sprintf('No customer with keyword [%s] in field [%s] page %d / %d!', str_replace('%', '', $keyword), $in[0], $page + 1, $pagemax));
            }

            throw new DomainException(sprintf('No customer with keyword [%s] in any field page %d / %d!', str_replace('%', '', $keyword), $page + 1, $pagemax));
        }

        return $customers;
    }

    public function countCustomers(): int
    {
        $sql = 'SELECT COUNT(*) AS nb FROM customers;';
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['nb'];
    }
}