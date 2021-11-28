<?php

/**
 * This file is part of the brandon14/unit-test-examples package.
 *
 * MIT License
 *
 * Copyright (c) 2018-2021 Brandon Clothier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

declare(strict_types=1);

namespace App\Services\Status\Providers;

use PDO;
use Throwable;
use RuntimeException;
use App\Contracts\Services\Status\StatusServiceProvider;

/**
 * Class PdoProvider.
 *
 * PDO status provider. Allows pinging a database connection created through
 * {@link \PDO} to test the connection.
 *
 * @author Brandon Clothier <brandon14125@gmail.com>
 */
class PdoProvider implements StatusServiceProvider
{
    /**
     * PDO connection instance. We use PDO here to decouple this provider from any 3rd party
     * library.
     */
    protected PDO $db;

    /**
     * Constructs a new database status service provider.
     *
     * @param \PDO $db PDO instance
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
                throw new RuntimeException('Unable to prepare PDO statement.');
            }

            // Execute query.
            $exec = $stmt->execute();

            // If the execute failed or returned an error code (this will not happen when
            // PDO is configured to throw exceptions, it will just throw which works as well)
            // then return an error status.
            if ($exec === false || $stmt->errorCode() !== '00000') {
                throw new RuntimeException('PDO statement execution failed.');
            }

            // Fetch the result (result is an array because of the fetch_type of FETCH_NUM).
            /** @var array $result */
            $result = $stmt->fetch(PDO::FETCH_NUM);

            if (! isset($result[0])) {
                throw new RuntimeException('Unable to retrieve query results.');
            }

            return ['status' => StatusServiceProvider::STATUS_OK];
        } catch (Throwable $t) {
            // Swallow exceptions on purpose.
        }

        return ['status' => StatusServiceProvider::STATUS_ERROR];
    }
}
