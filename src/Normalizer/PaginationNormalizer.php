<?php

namespace App\Normalizer;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface   
{

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]    
        private readonly NormalizerInterface $normalizer
    ){
    }       


/**
     * Normalizes data into a set of arrays/scalars.
     *
     * @param mixed                $data    Data to normalize
     * @param string|null          $format  Format the normalization result will be encoded as
     * @param array<string, mixed> $context Context options for the normalizer
     *
     * @return array|string|int|float|bool|\ArrayObject|null \ArrayObject is used to make sure an empty object is encoded as an object not an array
     *
     */    
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$data instanceof PaginationInterface) {
            throw new \LogicException('The data must be an instance of PaginationInterface.');
        }

        return [
            'items' => array_map(fn (Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context), $data->getItems()),
            'total' => $data->getTotalItemCount(),
            'page' => $data->getCurrentPageNumber(),
            'lastPage' => ceil($data->getTotalItemCount() / $data->getItemNumberPerPage())
        ];
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed                $data    Data to normalize
     * @param string|null          $format  The format being (de-)serialized from or into
     * @param array<string, mixed> $context Context options for the normalizer
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        //return $data instanceof PaginationInterface && 'json' === $format;
        return $data instanceof PaginationInterface;
    }

    /**
     * Returns the types potentially supported by this normalizer.
     *
     * For each supported formats (if applicable), the supported types should be
     * returned as keys, and each type should be mapped to a boolean indicating
     * if the result of supportsNormalization() can be cached or not
     * (a result cannot be cached when it depends on the context or on the data.)
     * A null value means that the normalizer does not support the corresponding
     * type.
     *
     * Use type "object" to match any classes or interfaces,
     * and type "*" to match any types.
     *
     * @return array<class-string|'*'|'object'|string, bool|null>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginationInterface::class => true,
        ];
    }
}
