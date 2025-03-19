<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EntityService
{
    protected $user;
    protected $session;

    public function __construct($user, $session)
    {
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * Выбор сущности (организация или группа).
     *
     * @param RequestInertia $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectOrganization($request)
    {
        try {
            $entityType = $request->input('entity_type', 'organization');
            $entityId = $request->input('organization_id');
            $roleId = $request->input('role_id');

            // Получаем сущность
            $entity = $this->getEntity($entityType, $entityId);
            if (!$entity) {
                throw new \Exception('Сущность не найдена', 404);
            }

            // Получаем имя сущности
            $entityName = $this->getEntityName($entity, $entityType);

            // Сохраняем данные в сессию
            $this->saveToSession($entityType, $entity, $entityName, $roleId);

            return response()->json(['message' => 'Сущность выбрана успешно']);
        } catch (\Exception $e) {
            Log::error('Ошибка при выборе сущности: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    /**
     * Получить сущность (организация или группа).
     *
     * @param string $entityType
     * @param int $entityId
     * @return mixed
     */
    protected function getEntity(string $entityType, int $entityId)
    {
        if ($entityType === 'organization') {
            return $this->user->organizations()->find($entityId);
        } elseif ($entityType === 'group') {
            return $this->user->groups()->find($entityId);
        }

        throw new \InvalidArgumentException('Неверный тип сущности', 400);
    }

    /**
     * Получить имя сущности.
     *
     * @param mixed $entity
     * @param string $entityType
     * @return string
     */
    protected function getEntityName($entity, string $entityType): string
    {
        if ($entityType === 'organization') {
            return $entity->full_name ?? '';
        } elseif ($entityType === 'group') {
            return $entity->name ?? '';
        }

        return 'Неизвестная сущность';
    }

    /**
     * Сохранить данные в сессию.
     *
     * @param string $entityType
     * @param mixed $entity
     * @param string $entityName
     * @param int $roleId
     */
    protected function saveToSession(string $entityType, $entity, string $entityName, int $roleId): void
    {
        $role = Role::find($roleId);
        if (!$role) {
            throw new \Exception('Роль не найдена', 404);
        }

        $this->session->put('selected_entity_type', $entityType);
        $this->session->put('selected_organization_id', $entity->id);
        $this->session->put('selected_organization_name', $entityName);
        $this->session->put('selected_organization_role_id', $roleId);
        $this->session->put('selected_organization_role_name', $role->name);
        $this->session->put('selected_organization_role_slug', $role->slug);
    }

    /**
     * Получить значения сессии, связанные с выбранной сущностью.
     *
     * @return array
     */
    public function getSelectedEntitySessionData(): array
    {
        return [
            'entity_type' => Session::get('selected_entity_type'),
            'organization_id' => Session::get('selected_organization_id'),
            'organization_name' => Session::get('selected_organization_name'),
            'role_id' => Session::get('selected_organization_role_id'),
            'role_name' => Session::get('selected_organization_role_name'),
            'role_slug' => Session::get('selected_organization_role_slug'),
        ];
    }
}
