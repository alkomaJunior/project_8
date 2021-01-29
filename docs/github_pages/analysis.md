# Analysis

[Back to summary](../index.md)

Respect [PSR-1](https://www.php-fig.org/psr/psr-1) and [PSR-12](https://www.php-fig.org/psr/psr-12).

## Launch analysis

The below commands will analyze the code in the ``src`` folder of the project & generate html output :

[**PHP Mess Detector**](https://phpmd.org/):
```shell
.\vendor\bin\phpmd .\src\ html .\phpmdRules.xml > .\analyzes\LTS\backend\messDetector\phpMDReport.html
```

[**Psalm**](https://psalm.dev/docs/):
```shell
vendor/bin/psalm  --show-info=true --output-format=xml | xsltproc vendor/roave/psalm-html-output/psalm-html-output.xsl - > .\analyzes\LTS\backend\psalm\psalm-report.html
```

[**PHPStan**](https://phpstan.org/user-guide/getting-started):
```shell
./vendor/bin/phpstan analyse -c phpstan.neon --error-format fileoutput
```

[**PHPMetrics**](https://www.phpmetrics.org/):
```shell
./vendor/bin/phpmetrics --config=phpMetricsConfig.json
```

## Fix errors

Execute the command line below to fix some errors automatically :

[**PHP Code Sniffer**](https://github.com/squizlabs/PHP_CodeSniffer)
```
./vendor/bin/phpcbf
```

[**Rector**](https://github.com/rectorphp/rector):
```shell
vendor/bin/rector process src/Controller --dry-run
```