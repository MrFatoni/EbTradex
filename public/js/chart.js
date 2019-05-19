let eChart;


function splitData(rawData) {
    let categoryData = [];
    let values = [];
    let volumes = [];
    // let a = 0;
    for (let i = 0; i < rawData.length; i++) {
        let categoryBuild = {
            value: rawData[i][0],
            textStyle: {
                fontSize: 10,
                color: '#00f'
            }
        }

        categoryData.push(categoryBuild);
        values.push([
            rawData[i][1],
            rawData[i][2],
            rawData[i][3],
            rawData[i][4]
        ]);
    }

    return {
        categoryData: categoryData,
        values: values,
        volumes: volumes
    };
}

function calculateMACD(data) {
    let result = [];
    let y = 0;
    let z = 0;
    let dayFirst = 12;
    let daySecond = 26;
    for (let i = 0, len = data.values.length; i < len; i++) {
        y = i < dayFirst ? i+1 :dayFirst;
        z = i < daySecond ? i+1 :daySecond;
        let sum1 = 0;
        let sum2 = 0;
        for (let j = 0; j < z; j++) {
            sum2 += parseFloat(data.values[i - j][1]);
            if(j<y){
                sum1 += parseFloat(data.values[i - j][1]);
            }
        }
        let macd = (sum1 / y) - (sum2 / z);
        result.push([i, +macd.toFixed(3), macd <= 0 ? -1 : 1]);
    }
    return result;
}

function calculateMA(dayCount, data) {
    let result = [];
    for (let i = 0, len = data.values.length; i < len; i++) {
        if (i < dayCount) {
            result.push('0');
            continue;
        }
        let sum = 0;
        for (let j = 0; j < dayCount; j++) {
            sum += parseFloat(data.values[i - j][1]);
        }

        let value = (sum / dayCount).toFixed(3) || 0;
        result.push(parseFloat(value));
    }
    return result;
}

function calculateZoom() {
    let zoomArray = [360, 1440, 2880, 5760, 10080, 20160, 43200];

    let visibleCandlesticks = chartData.length;

    if (zoomArray.indexOf(defaultZoom) >= 0) {
        visibleCandlesticks = parseInt(defaultZoom / defaultInterval);
    }

    if (visibleCandlesticks < 1) {
        visibleCandlesticks = 1;
    }

    return chartData.length <= visibleCandlesticks ? 0 : (100 - (visibleCandlesticks - 1) * 100 / chartData.length).toFixed(8);
}

function makeChart(element, data) {
    let upColor = '#0055cc';
    let downColor = '#ff5500';
    let echartData = splitData(data);
    let zoom = calculateZoom();
    eChart = echarts.init(element);

    // specify chart configuration item and data
    let option = {
        textStyle: {
            color: '#00f',
            fontSize: 10
        },
        color: ['#00ff00','#0000ff','#ff0000'],
        backgroundColor: '#ffffff',
        animation: false,
        legend: {
            data: ['MA12', 'MA40', 'MA200']
        },
        tooltip: {
            trigger: 'axis',
            triggerOn: 'click',
            axisPointer: {
                type: 'cross'
            },
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            borderWidth: 1,
            borderColor: '#0099ff',
            padding: 10,
            textStyle: {
                color: '#000',
                fontSize: 10
            },
            position: function (pos, params, el, elRect, size) {
                let obj = {top: '10.05%'};
                obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = '30';
                return obj;
            }
            // extraCssText: 'width: 170px'
        },
        axisPointer: {
            link: {xAxisIndex: 'all'},
            label: {
                backgroundColor: '#777'
            }
        },
        toolbox: {
            show: false
        },
        visualMap: {
            show: false,
            seriesIndex: 4,
            dimension: 2,
            pieces: [{
                value: 1,
                color: upColor
            }, {
                value: -1,
                color: downColor
            }]
        },
        grid: [
            {
                left: '7%',
                right: '0',
                height: '66%',
                top: '7%'
            },
            {
                left: '7%',
                right: '0',
                top: '78%',
                height: '13%'
            }
        ],
        xAxis: [
            {
                type: 'category',
                data: echartData.categoryData,
                scale: true,
                boundaryGap: true,
                axisLine: {
                    lineStyle: {
                        color: '#08f',
                        type: 'dotted'
                    }
                },
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: '#0df',
                        type: 'dotted'
                    }
                },
                splitNumber: 10,
                /*min: 'dataMin',
                        max: 'dataMax',*/
                axisPointer: {
                    z: 100
                }
            },
            {
                type: 'category',
                gridIndex: 1,
                data: echartData.categoryData,
                scale: true,
                boundaryGap: true,
                axisLine: {
                    show: false
                },
                axisTick: {show: false},
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: '#08f',
                        opacity: 0.4,
                        type: 'dotted'
                    }
                },
                axisLabel: {show: false},
                splitNumber: 1/*,
                            min: 'dataMin',
                            max: 'dataMax'*/
            }
        ],
        yAxis: [
            {
                axisLine: {
                    lineStyle: {
                        color: '#08f',
                        type: 'dotted'
                    }
                },
                scale: true,
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: '#0df',
                        type: 'dotted'
                    }
                },
                splitNumber: 5,
                axisLabel: {
                    fontSize: 10
                }
            },
            {
                type: 'value',
                scale: true,
                gridIndex: 1,
                splitNumber: 1,
                splitLine: {
                    show: true,
                    lineStyle: {
                        color: '#54e1cb',
                        opacity: 0.4,
                        type: 'dotted'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: '#54e1cb',
                        opacity: 0.4,
                        type: 'dotted'
                    }
                },
                axisTick: {show: false},
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: '#a6ffeb',
                        opacity: 0.4
                    }
                },
                axisLabel: {show: false}
            }
        ],
        dataZoom: [
            {
                // type: 'inside',ph
                xAxisIndex: [0, 1],
                start: zoom,
                end: 100
            },
            {
                show: true,
                xAxisIndex: [0, 1],
                type: 'slider',
                top: '92%',
                start: zoom,
                end: 100
            }
        ],
        series: [
            {
                name: 'Chart Data',
                type: 'candlestick',
                data: echartData.values,
                itemStyle: {
                    normal: {
                        color: upColor,
                        color0: downColor,
                        borderColor: upColor,
                        borderColor0: downColor
                    }
                },
                tooltip: {
                    formatter: function (param) {
                        param = param[0];
                        return [
                            'Date: ' + param.name + '<hr size=1 style="margin: 3px 0">',
                            'Open: ' + param.data[0] + '<br/>',
                            'Close: ' + param.data[1] + '<br/>',
                            'Lowest: ' + param.data[2] + '<br/>',
                            'Highest: ' + param.data[3] + '<br/>'
                        ].join('');
                    }
                }
            },
            {
                name: 'MA12',
                type: 'line',
                data: calculateMA(12, echartData),
                smooth: true,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        opacity: 0.5,
                        width: 1,
                        color: '#00ff00'
                    }
                }
            },
            {
                name: 'MA40',
                type: 'line',
                data: calculateMA(40, echartData),
                smooth: true,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        opacity: 0.5,
                        width: 1,
                        color: '#0000ff'
                    }
                }
            },
            {
                name: 'MA200',
                type: 'line',
                data: calculateMA(200, echartData),
                smooth: true,
                showSymbol: false,
                lineStyle: {
                    normal: {
                        opacity: 0.5,
                        width: 1,
                        color: '#ff0000'
                    }
                }
            },
            {
                name: 'Change',
                type: 'bar',
                barWidth: '55%',
                xAxisIndex: 1,
                yAxisIndex: 1,
                data: calculateMACD(echartData)
            }
        ]
    };

    // use configuration item and data specified to show chart
    eChart.setOption(option);
}