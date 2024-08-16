<?php

declare(strict_types=1);

namespace Dagger\Telemetry;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Trace\SpanExporter\LoggerExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

final class Telemetry
{
    private readonly TracerInterface $tracer;

    public function __construct()
    {
        //        $tracerProvider = new TracerProvider(new BatchSpanProcessor(
        //                    exporter: new LoggerExporter('dagger-php-sdk'),
        //                    clock: ClockFactory::getDefault(),
        //                ));

        $transport = (new OtlpHttpTransportFactory())->create(
            getenv('OTEL_EXPORTER_OTLP_TRACES_ENDPOINT'),
            'application/x-protobuf',
        );
        $exporter = new SpanExporter($transport);
        $spanProcessor = new SimpleSpanProcessor($exporter);
        $tracerProvider = new TracerProvider($spanProcessor);



        $this->tracer = $tracerProvider->getTracer(
            name: 'dagger.io/sdk.php',
            schemaUrl: null,
        );
    }

    public function getTracer(): TracerInterface
    {
        return $this->tracer;
    }
}
