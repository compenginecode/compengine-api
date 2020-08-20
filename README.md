## CompEngine API
This is the API for [CompEngine](https://www.comp-engine.org/).
You can set it up by building the docker container via "docker build" and inject any environment variables with the same names as those options in ``/api/private/configuration/configuration.ini`.

The public API to the hosted version of CompEngine can [be seen here](https://www.comp-engine.org/#!the-public-api).

## System requirements

This project runs on PHP 5.6, MySQL 5.6 or MySQL 5.7, Redis and Elasticsearch version 2. The system resources required depends on the number of time series present. 

## Setup the database
The database, Redis and Elasticsearch must be running before the database is setup.

To update the current database according to the XML definitions run:

    php vendor/doctrine/orm/bin/doctrine orm:schema-tool:update --dump-sql --force

To regenerate all proxies run:

    php vendor/doctrine/orm/bin/doctrine orm:generate-proxies

To install:

    php install.php && cd [..]imperial-college-api\private\processes\ingest
        && php generate-intermediates.php "[..]imperial-college-api\private\sample\originals
        
Finally, make sure the absolute path to the C feature generation program is correct. This is stored in the database inside the ``feature_vector_families`` table in the column ``generator_script_path``. This should be something like "php /var/my_c_program".

Output style:

        1.00000000000000, CO_Embed2_Basic_tau.incircle_1, 15.745000
        1.00000000000000, CO_Embed2_Basic_tau.incircle_2, 14.182000
        3.00000000000000, FC_LocalSimple_mean1.taures, 13.349000
        -0.11301881609223, DN_HistogramMode_10, 0.336000
        0.51325434262684, SY_StdNthDer_1, 0.172000
        -0.28389786869818, AC_9, 12.078000
        1.49794872640501, SB_MotifTwo_mean.hhh, 2.149000
        10, CO_FirstMin_ac, 11.353000
        0.02243162108072, DN_OutlierInclude_abs_001.mdrmd, 28.714000
        -0.10069031152189, CO_trev_1.num, 0.713000
        4.54000000000000, SY_SpreadRandomLocal_50_100.meantaul, 4.498000
        0.24489795918367, SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1, 7.190000
        0.14528773978315, PH_Walker_prop_01.sw_propcross, 0.104000
        2.52000000000000, SY_SpreadRandomLocal_ac2_100.meantaul, 9.595000
        2.00000000000000, FC_LocalSimple_lfit.taures, 15.363000
        0.82131403309804, EN_SampEn_5_03.sampen1, 178.556000
        
## License
Copyright 2020 Ben D. Fulcher

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
