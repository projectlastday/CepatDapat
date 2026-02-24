<?php $__env->startSection('content'); ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold mb-0">Statistik Harian</h5>
                            <small class="text-muted" id="dailySubtitle">Total Lelang Baru Hari Ini</small>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="dailyChartType" style="width: auto;">
                                <option value="bar" selected>Bar Chart</option>
                                <option value="area">Area Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>
                            <select class="form-select form-select-sm" id="dailyDataSource" style="width: auto;">
                                <option value="today_lelang" selected>Total Lelang Baru (Hari Ini)</option>
                                <option value="today_transaksi">Total Transaksi (Hari Ini)</option>
                                <option value="yesterday_lelang">Total Lelang Baru (Kemarin)</option>
                                <option value="yesterday_transaksi">Total Transaksi (Kemarin)</option>
                            </select>
                        </div>
                    </div>
                    <div id="dailyChart"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold mb-0">Statistik Bulanan</h5>
                            <small class="text-muted" id="monthlySubtitle">Total Lelang Baru Bulan Ini</small>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="monthlyChartType" style="width: auto;">
                                <option value="bar" selected>Bar Chart</option>
                                <option value="area">Area Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>
                            <select class="form-select form-select-sm" id="monthlyDataSource" style="width: auto;">
                                <option value="this_month_lelang" selected>Total Lelang Baru (Bulan Ini)</option>
                                <option value="this_month_transaksi">Total Transaksi (Bulan Ini)</option>
                                <option value="last_month_lelang">Total Lelang Baru (Bulan Lalu)</option>
                                <option value="last_month_transaksi">Total Transaksi (Bulan Lalu)</option>
                            </select>
                        </div>
                    </div>
                    <div id="monthlyChart"></div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = <?php echo json_encode($all_chart_data, 15, 512) ?>;

            // ----- Label map for subtitles -----
            const dailyLabels = {
                'today_lelang': 'Total Lelang Baru Hari Ini',
                'today_transaksi': 'Total Transaksi Hari Ini',
                'yesterday_lelang': 'Total Lelang Baru Kemarin',
                'yesterday_transaksi': 'Total Transaksi Kemarin',
            };
            const monthlyLabels = {
                'this_month_lelang': 'Total Lelang Baru Bulan Ini',
                'this_month_transaksi': 'Total Transaksi Bulan Ini',
                'last_month_lelang': 'Total Lelang Baru Bulan Lalu',
                'last_month_transaksi': 'Total Transaksi Bulan Lalu',
            };

            // ----- Shared chart config builder -----
            function buildOptions(type, categories, data, seriesName, isCurrency) {
                // === PIE CHART ===
                if (type === 'pie') {
                    // Filter out zero-value entries so pie chart only shows slices with data
                    let filteredLabels = [];
                    let filteredData = [];
                    for (let i = 0; i < data.length; i++) {
                        if (data[i] > 0) {
                            filteredLabels.push(categories[i]);
                            filteredData.push(data[i]);
                        }
                    }
                    // If no data at all, show a placeholder
                    if (filteredData.length === 0) {
                        filteredLabels = ['Tidak ada data'];
                        filteredData = [0];
                    }
                    return {
                        chart: {
                            type: 'pie',
                            height: 400,
                            toolbar: { show: false },
                        },
                        labels: filteredLabels,
                        series: filteredData,
                        legend: {
                            position: 'bottom',
                            fontSize: '13px',
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val, opts) {
                                return opts.w.config.labels[opts.seriesIndex] + ': ' + Math.round(val) + '%';
                            },
                        },
                        tooltip: {
                            theme: 'dark',
                            y: {
                                formatter: function (val) {
                                    if (isCurrency) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                    }
                                    return val + ' lelang';
                                }
                            }
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: { width: 300 },
                                legend: { position: 'bottom' }
                            }
                        }]
                    };
                }

                // === BAR / AREA CHART ===
                return {
                    chart: {
                        type: type,
                        height: 350,
                        toolbar: { show: false },
                        animations: { enabled: true },
                        selection: { enabled: false },
                    },
                    plotOptions: {
                        bar: { horizontal: false, columnWidth: '50%', borderRadius: 4 }
                    },
                    dataLabels: { enabled: false },
                    colors: ['#673ab7'],
                    fill: {
                        type: type === 'area' ? 'gradient' : 'solid',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: type === 'bar' ? 0 : 3
                    },
                    series: [{ name: seriesName, data: data }],
                    legend: { show: false },
                    xaxis: { categories: categories },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                if (isCurrency) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                }
                                return Math.floor(val).toString();
                            }
                        }
                    },
                    grid: { strokeDashArray: 4 },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (val) {
                                if (isCurrency) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                }
                                return val + ' lelang';
                            }
                        }
                    },
                    states: {
                        hover: { filter: { type: 'none' } },
                        active: { allowMultipleDataPointsSelection: false, filter: { type: 'none' } }
                    }
                };
            }

            function isCurrencyKey(key) {
                return key.includes('transaksi');
            }

            // ===== Helper to rebuild chart =====
            function rebuildChart(chartInstance, selector, type, categories, data, label, isCurrency) {
                chartInstance.destroy();
                let newChart = new ApexCharts(
                    document.querySelector(selector),
                    buildOptions(type, categories, data, label, isCurrency)
                );
                newChart.render();
                return newChart;
            }

            // ===== DAILY CHART =====
            let dailySource = 'today_lelang';
            let dailyType = 'bar';
            let dailyChart = new ApexCharts(
                document.querySelector('#dailyChart'),
                buildOptions(dailyType, chartData.hourly.categories, chartData.hourly.series[dailySource], dailyLabels[dailySource], isCurrencyKey(dailySource))
            );
            dailyChart.render();

            document.getElementById('dailyChartType').addEventListener('change', function () {
                dailyType = this.value;
                dailyChart = rebuildChart(dailyChart, '#dailyChart', dailyType, chartData.hourly.categories, chartData.hourly.series[dailySource], dailyLabels[dailySource], isCurrencyKey(dailySource));
            });

            document.getElementById('dailyDataSource').addEventListener('change', function () {
                dailySource = this.value;
                document.getElementById('dailySubtitle').textContent = dailyLabels[dailySource];
                dailyChart = rebuildChart(dailyChart, '#dailyChart', dailyType, chartData.hourly.categories, chartData.hourly.series[dailySource], dailyLabels[dailySource], isCurrencyKey(dailySource));
            });

            // ===== MONTHLY CHART =====
            let monthlySource = 'this_month_lelang';
            let monthlyType = 'bar';
            let monthlyChart = new ApexCharts(
                document.querySelector('#monthlyChart'),
                buildOptions(monthlyType, chartData.daily.categories, chartData.daily.series[monthlySource], monthlyLabels[monthlySource], isCurrencyKey(monthlySource))
            );
            monthlyChart.render();

            document.getElementById('monthlyChartType').addEventListener('change', function () {
                monthlyType = this.value;
                monthlyChart = rebuildChart(monthlyChart, '#monthlyChart', monthlyType, getMonthlyCategories(monthlySource), chartData.daily.series[monthlySource], monthlyLabels[monthlySource], isCurrencyKey(monthlySource));
            });

            document.getElementById('monthlyDataSource').addEventListener('change', function () {
                monthlySource = this.value;
                document.getElementById('monthlySubtitle').textContent = monthlyLabels[monthlySource];
                monthlyChart = rebuildChart(monthlyChart, '#monthlyChart', monthlyType, getMonthlyCategories(monthlySource), chartData.daily.series[monthlySource], monthlyLabels[monthlySource], isCurrencyKey(monthlySource));
            });

            function getMonthlyCategories(source) {
                if (source.startsWith('last_month')) {
                    return chartData.daily.categories_last;
                }
                return chartData.daily.categories;
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\dashboard.blade.php ENDPATH**/ ?>