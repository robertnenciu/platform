<?php

namespace Oro\Bundle\ActivityListBundle\Model\Strategy;

use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\ActivityListBundle\Entity\Manager\ActivityListManager;
use Oro\Bundle\ActivityListBundle\Model\MergeModes;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityMergeBundle\Data\FieldData;
use Oro\Bundle\EntityMergeBundle\Model\Strategy\StrategyInterface;
use Oro\Component\PhpUtils\ArrayUtil;
use Symfony\Component\Security\Acl\Util\ClassUtils;

class UniteStrategy implements StrategyInterface
{
    /** @var ActivityListManager  */
    protected $activityListManager;

    /** @var DoctrineHelper  */
    protected $doctrineHelper;

    /**
     * @param ActivityListManager $activityListManager
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(ActivityListManager $activityListManager, DoctrineHelper $doctrineHelper)
    {
        $this->activityListManager = $activityListManager;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(FieldData $fieldData)
    {
        $entityData    = $fieldData->getEntityData();
        $masterEntity  = $entityData->getMasterEntity();
        $fieldMetadata = $fieldData->getMetadata();

        $entities = $fieldData->getEntityData()->getEntities();
        foreach ($entities as $sourceEntity) {
            if ($sourceEntity->getId() !== $masterEntity->getId()) {
                $entityClass = ClassUtils::getRealClass($masterEntity);
                $activityClass = $fieldMetadata->get('type');
                $queryBuilder = $this->doctrineHelper
                    ->getEntityRepository(ActivityList::ENTITY_NAME)
                    ->getActivityListQueryBuilderByActivityClass($entityClass, $sourceEntity->getId(), $activityClass);

                $activityListItems = $queryBuilder->getQuery()->getResult();

                $activityIds = ArrayUtil::arrayColumn($activityListItems, 'id');
                $this->activityListManager
                    ->replaceActivityTargetWithPlainQuery(
                        $activityIds,
                        $entityClass,
                        $sourceEntity->getId(),
                        $masterEntity->getId()
                    );

                $activityIds = ArrayUtil::arrayColumn($activityListItems, 'relatedActivityId');
                $this->activityListManager
                    ->replaceActivityTargetWithPlainQuery(
                        $activityIds,
                        $entityClass,
                        $sourceEntity->getId(),
                        $masterEntity->getId(),
                        $activityClass
                    );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FieldData $fieldData)
    {
        return $fieldData->getMode() === MergeModes::ACTIVITY_UNITE;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'activity_unite';
    }
}
