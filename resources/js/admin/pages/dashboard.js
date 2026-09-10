document.addEventListener("DOMContentLoaded", function() {

    function formatRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    function initPendapatanChart(data) {
        if (!document.querySelector("#chartPendapatanBulanan")) return null;

        const optionsPendapatan = {
            chart: {
                type: "area",
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            series: data.seriesPendapatan,
            colors: ["#4E6BFF", "#0dcaf0"],
            stroke: {
                curve: "smooth",
                width: 3,
                lineCap: "round",
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "light",
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ["#4E6BFF", "#0dcaf0"],
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                categories: data.labelBulan,
                labels: {
                    style: {
                        colors: "#6c757d",
                        fontSize: "12px",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                axisBorder: {
                    show: true,
                    color: "#e9ecef",
                    height: 1,
                },
                axisTicks: {
                    show: true,
                    color: "#e9ecef",
                },
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        if (value >= 1000000) {
                            return (value / 1000000).toFixed(1) + " jt";
                        } else if (value >= 1000) {
                            return (value / 1000).toFixed(0) + " rb";
                        }
                        return value;
                    },
                    style: {
                        colors: "#6c757d",
                        fontSize: "12px",
                        fontFamily: "Inter, sans-serif",
                    },
                },
                axisBorder: {
                    show: true,
                    color: "#e9ecef",
                },
            },
            grid: {
                show: true,
                strokeDashArray: 3,
                borderColor: "#e9ecef",
                padding: {
                    top: 0,
                    right: 10,
                    bottom: 0,
                    left: 10,
                },
            },
            legend: {
                show: true,
                position: "top",
                horizontalAlign: "right",
                fontSize: "12px",
                fontFamily: "Inter, sans-serif",
                markers: {
                    width: 8,
                    height: 8,
                    radius: 4,
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: "light",
                x: {
                    show: true,
                    formatter: function (
                        value,
                        { series, seriesIndex, dataPointIndex, w }
                    ) {
                        return w.globals.categoryLabels[dataPointIndex];
                    },
                },
                y: {
                    formatter: function (value) {
                        return formatRupiah(value);
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            markers: {
                size: 4,
                colors: ["#fff"],
                strokeColors: ["#4E6BFF", "#0dcaf0"],
                strokeWidth: 2,
                hover: {
                    size: 6,
                },
            },
        };

        const chartPendapatan = new ApexCharts(
            document.querySelector("#chartPendapatanBulanan"),
            optionsPendapatan
        );
        chartPendapatan.render();
        return chartPendapatan;
    }

    function initTransaksiChart(data) {
        if (!document.querySelector("#chartTotalTransaksi")) return null;

        const optionsTransaksi = {
            chart: {
                type: "donut",
                height: 320,
                offsetY: 0,
            },
            series: data.seriesTransaksi,
            labels: ["Penjualan", "Pembelian"],
            colors: ["#4E6BFF", "#198754"],
            plotOptions: {
                pie: {
                    donut: {
                        size: "75%",
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: "1rem",
                                color: "#6b7280",
                                offsetY: -10,
                            },
                            value: {
                                show: true,
                                fontSize: "1.6rem",
                                fontWeight: "bold",
                                color: "#1f2937",
                                offsetY: 10,
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: "Total",
                                color: "#4b5563",
                                fontSize: "1.1rem",
                                fontWeight: 700,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce(
                                        (a, b) => a + b,
                                        0
                                    );
                                },
                            },
                        },
                    },
                },
            },
            legend: { show: false },
            dataLabels: { enabled: false },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + " Transaksi";
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 1200,
                    options: {
                        chart: { height: 300 },
                    },
                },
                {
                    breakpoint: 992,
                    options: {
                        chart: { height: 280 },
                        plotOptions: {
                            pie: {
                                donut: { size: "70%" },
                            },
                        },
                    },
                },
                {
                    breakpoint: 768,
                    options: {
                        chart: { height: 260 },
                    },
                },
                {
                    breakpoint: 576,
                    options: {
                        chart: { height: 240 },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        value: { fontSize: "1.2rem" },
                                        total: { fontSize: "0.9rem" },
                                    },
                                },
                            },
                        },
                    },
                },
                {
                    breakpoint: 480,
                    options: {
                        chart: { height: 220 },
                    },
                },
                {
                    breakpoint: 400,
                    options: {
                        chart: { height: 200 },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: "65%",
                                    labels: {
                                        value: { fontSize: "1.1rem" },
                                        total: { fontSize: "0.85rem" },
                                    },
                                },
                            },
                        },
                    },
                },
            ],
        };

        const chartTransaksi = new ApexCharts(
            document.querySelector("#chartTotalTransaksi"),
            optionsTransaksi
        );
        chartTransaksi.render();
        return chartTransaksi;
    }

    function initFilterForm() {
        const filterForm = document.getElementById("filterForm");
        if (!filterForm) return;

        document.getElementById("year").addEventListener("change", function () {
            filterForm.submit();
        });

        document
            .getElementById("month")
            .addEventListener("change", function () {
                filterForm.submit();
            });

        const branchSelect = document.getElementById("branch_id");
        if (branchSelect) {
            branchSelect.addEventListener("change", function () {
                filterForm.submit();
            });
        }
    }

    function initClickableRows() {
        document.querySelectorAll(".clickable-row").forEach((row) => {
            row.addEventListener("click", function (e) {
                const url = this.getAttribute("data-detail-url");
                if (
                    url &&
                    !e.target.closest("a, button, input, select, textarea")
                ) {
                    window.location.href = url;
                }
            });
        });
    }

    function initDashboard() {
        const dashboardData = window.dashboardData || {
            seriesPendapatan: [[], []],
            seriesTransaksi: [0, 0],
            labelBulan: [],
        };

        console.log("Initializing dashboard with data:", dashboardData);

        const pendapatanChart = initPendapatanChart(dashboardData);
        const transaksiChart = initTransaksiChart(dashboardData);

        initFilterForm();
        initClickableRows();

        window.dashboardCharts = {
            pendapatan: pendapatanChart,
            transaksi: transaksiChart,
        };
    }

    document.addEventListener("DOMContentLoaded", initDashboard);

    function cleanupDashboard() {
        if (window.dashboardCharts) {
            if (window.dashboardCharts.pendapatan) {
                window.dashboardCharts.pendapatan.destroy();
            }
            if (window.dashboardCharts.transaksi) {
                window.dashboardCharts.transaksi.destroy();
            }
        }
        window.dashboardCharts = null;
    }

    if (typeof module !== "undefined" && module.exports) {
        module.exports = {
            initDashboard,
            cleanupDashboard,
            initPendapatanChart,
            initTransaksiChart,
            initFilterForm,
            initClickableRows,
            formatRupiah,
        };
    }

    initDashboard();
});
