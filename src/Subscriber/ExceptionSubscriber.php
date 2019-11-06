<?php declare(strict_types=1);


namespace App\Subscriber;


use App\Exception\ValidateException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['placeResponse', 0],
            ],
        ];
    }

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ExceptionSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function placeResponse(ExceptionEvent $event)
    {
        $exception = $event->getException();

        if($exception instanceof ValidateException) {
            $event->setResponse(new JsonResponse(
                ['success' => 0, 'error' => $exception->getMessage()],
                $exception->getCode()
            ));
        }

        if (preg_match('@application/json@', $event->getRequest()->headers->get('accept'))) {
            $this->logger->error((string) $exception);

            $event->setResponse(new JsonResponse(
                ['success' => 0, 'error' => 'internal server error'],
                500
            ));
        }
    }
}
