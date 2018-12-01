<?php

declare(strict_types=1);

namespace App\Services\Status\Providers;

use PDO;
use PDOException;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * PDO status provider. Allows pinging a database connection created through
 * {@link \PDO} to test the connection.
 *
 * @author    Brandon Clothier <brandon14125@gmail.com>
 *
 * @version   1.0.0
 *
 * @license   MIT
 * @copyright 2018
 */
class PdoProvider implements StatusServiceProvider
{
    /**
     * PDO connection instance. We use PDO here to decouple this provider from any 3rd party
     * library.
     *
     * @var \PDO
     */
    protected $db;

    /**
     * Constructs a new database status service provider.
     *
     * @param \PDO $db
     *
     * @return void
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): array
    {
        try {
            // Execute a query to test the database connection is alive.
            $stmt = $this->db->query('SELECT 1+1');

            // Could not create prepared statement.
            if ($stmt === false) {
                return ['status' => StatusServiceProvider::STATUS_ERROR];
            }

            // Execute query.
            $exec = $stmt->execute();

            // If the execute failed or returned an error code (this will not happen when
            // PDO is configured to throw exceptions, it will just throw which works as well)
            // then return an error status.
            if ($exec === false || $stmt->errorCode() !== '00000') {
                return ['status' => StatusServiceProvider::STATUS_ERROR];
            }

            // Fetch the result.
            $result = $stmt->fetch(PDO::FETCH_NUM);

            if (! isset($result[0])) {
                return ['status' => StatusServiceProvider::STATUS_ERROR];
            }

            return ['status' => StatusServiceProvider::STATUS_OK];
        } catch (PDOException $e) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
