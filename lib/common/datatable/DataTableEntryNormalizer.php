<?php


namespace CsrDelft\common\datatable;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use function common\short_class;

/**
 * Als er een object genormalizeerd wordt met interface DataTableEntry, voeg dan het veld UUID toe.
 *
 * @package CsrDelft\common
 */
class DataTableEntryNormalizer implements ContextAwareNormalizerInterface {
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var ObjectNormalizer
	 */
	private $normalizer;

	public function __construct(EntityManagerInterface $entityManager, ObjectNormalizer $normalizer) {
		$this->entityManager = $entityManager;
		$this->normalizer = $normalizer;
	}

	public function normalize($topic, string $format = null, array $context = []) {
		$metadata = $this->entityManager->getClassMetadata(get_class($topic));

		$data = $this->normalizer->normalize($topic, $format, $context);

		$data['UUID'] = strtolower(sprintf('%s@%s.csrdelft.nl', implode('.', $metadata->getIdentifierValues($topic)), short_class($topic)));

		return $data;
	}

	public function supportsNormalization($data, string $format = null, array $context = []) {
		return $data instanceof DataTableEntry;
	}
}
