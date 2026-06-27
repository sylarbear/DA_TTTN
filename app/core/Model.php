<?php

/**
 * Base Model
 * Cung cấp các method CRUD dùng chung
 */
class Model
{
    /** @var PDO */
    protected $db;

    /** @var string */
    protected $table;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả bản ghi
     * @param  string $orderBy Sắp xếp
     * @return array
     */
    public function all(string $orderBy = 'id ASC'): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");

        return $stmt->fetchAll();
    }

    /**
     * Tìm bản ghi theo ID
     * @param  int         $id
     * @return array|false
     */
    public function find(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Tìm bản ghi theo điều kiện
     * @param  string      $column Tên cột
     * @param  mixed       $value  Giá trị
     * @return array|false
     */
    public function findBy(string $column, $value)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);

        return $stmt->fetch();
    }

    /**
     * Tìm nhiều bản ghi theo điều kiện
     * @param  string $column
     * @param  mixed  $value
     * @param  string $orderBy
     * @return array
     */
    public function where(string $column, $value, string $orderBy = 'id ASC'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY {$orderBy}");
        $stmt->execute(['value' => $value]);

        return $stmt->fetchAll();
    }

    /**
     * Thêm bản ghi mới
     * @param  array $data Mảng key => value
     * @return int   ID của bản ghi mới
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Cập nhật bản ghi
     * @param  int   $id
     * @param  array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "{$key} = :{$key}, ";
        }
        $setClause = rtrim($setClause, ', ');

        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$setClause} WHERE id = :id");

        return $stmt->execute($data);
    }

    /**
     * Xóa bản ghi
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đếm số bản ghi
     * @param  string|null $column
     * @param  mixed|null  $value
     * @return int
     */
    public function count(?string $column = null, $value = null): int
    {
        if ($column && $value !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = :value");
            $stmt->execute(['value' => $value]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        }

        return (int) $stmt->fetch()['total'];
    }

    /**
     * Thực thi raw query
     * @param  string       $sql
     * @param  array        $params
     * @return PDOStatement
     */
    public function raw(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }
}
