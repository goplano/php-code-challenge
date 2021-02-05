<template>
    <div class="w-full">
        <table class="group-table">
            <tr v-for="(item,index) in groupedData" class="group-table-row">
                <td v-for="field in fields" class="group-table-cell" :class="getClass(item, field, index)">
                    {{ format(field, item[field]) }}
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import accounting from "accounting";

export default {
    name: "GroupTable",
    props: {
        fields: {
            type: Array,
            default: function () {
                return [];
            },
            required: true
        },
        items: {
            type: Array,
            default: function () {
                return [];
            },
            required: true
        },
        groupBy: {
            type: String,
            default: null,
            required: false
        },
        groupHeaderColumn: {
            type: String,
            default: null,
            required: false
        },
        totals: {
            type: Array,
            default: function () {
                return [];
            },
            required: false
        },
        headers: {
            type: Array,
            default: function () {
                return [];
            },
            required: false
        },
        currency: {
            type: Array,
            default: function () {
                return [];
            },
            required: false
        },
    },
    data() {
        return {}
    },
    methods: {
        getTotalsForKey(field, data) {
            let sum = 0;
            Object.keys(data).forEach(key => {
                sum += data[key][field]
            });
            return sum;
        },
        makeRow(defaults = {}, isHeader = false) {
            let obj = {};
            obj.isHeader = isHeader;
            this.fields.forEach(field => {
                obj[field] = "";
            });
            Object.keys(defaults).forEach(field => obj[field] = defaults[field]);
            return obj;
        },
        intializeTotals() {
            if (this.hasTotals) {
                let obj = {};
                this.totals.forEach((field) => obj[field] = 0);
            }
        },
        format(field, value) {
            if (value === "" || value === undefined || this.headers.includes(value) || this.fields.includes(value)) return value;
            if (this.currency.includes(field)) {
                return this.formatMoney(value);
            }
            return value;
        },
        formatMoney(value) {
            return accounting.formatMoney(value);
        },
        getHeader(currGroup) {
            let header = {};
            header.isHeader = true;
            this.fields.forEach((field) => {
                let headerText = field;
                const idx = this.fields.indexOf(field);
                if (idx >= 0 && idx < this.headers.length) {
                    headerText = this.headers[idx];
                }
                header[field] = headerText;
            });
            if (this.hasGroupBy) {
                header[(this.groupHeaderColumn !== null) ? this.groupHeaderColumn : this.groupBy] = currGroup;
            }
            return header;
        },
        getClass(item, field, index) {
            let classes = [];
            if (item.hasOwnProperty('isHeader') && item.isHeader === true) {
                classes = ["group-table-header-cell"];
            } else {
                if (index % 2 === 0) {
                    classes = ['group-table-even']
                } else {
                    classes = ['group-table-odd'];
                }
            }
            if (this.currency.includes(field)) {
                classes.push("group-table-currency");
            }
            return classes;
        }
    },
    computed: {
        hasGroupBy() {
            return this.groupBy !== null;
        },
        hasTotals() {
            return this.totals.length > 0;
        },
        groupedData() {
            if (!this.hasGroupBy && !this.hasTotals) {
                return this.sortedData;
            }

            let result = [];
            let currGroup = '';
            let totals = {};

            this.sortedData.forEach((item) => {


                if (item[this.groupBy] !== currGroup) {
                    if (this.hasTotals) {
                        totals[item[this.groupBy]] = {};
                        this.totals.forEach((field) => totals[item[this.groupBy]][field] = 0);
                    }

                    if (result.length > 0) {
                        if (this.hasTotals) {
                            let defaults = {};
                            defaults[this.groupHeaderColumn] = 'Totals:';
                            this.totals.forEach((field) => defaults[field] = totals[currGroup][field]);

                            result.push(this.makeRow(defaults));
                        }
                        result.push(this.makeRow());
                    }
                    currGroup = item[this.groupBy];
                    // print header
                    let header = this.getHeader(currGroup);
                    result.push(header);

                }
                result.push(item);
                if (this.hasTotals) {
                    this.totals.forEach((field) => totals[item[this.groupBy]][field] += item[field]);
                }


            });
            if (result.length > 0) {
                if (this.hasTotals) {
                    let defaults = {};
                    defaults[this.groupHeaderColumn] = 'Totals';
                    this.totals.forEach((field) => defaults[field] = totals[currGroup][field]);
                    result.push(this.makeRow(defaults));
                }
            }
            return result;
        },
        sortedData() {
            return this.items.sort((a, b) => {
                if (a[this.groupBy] < b[this.groupBy]) {
                    return -1;
                }
                if (a[this.groupBy] > b[this.groupBy]) {
                    return 1;
                }
                return 0
            })
        }
    }
}
</script>

<style>
.group-table {
    width: 100%;
    table-layout: auto;
    border: 1px solid black;
    border-collapse: collapse;
}

.group-table-cell {
    border: 1px solid black;
    padding: .25rem .75rem;
    height: 1.2rem;
}

.group-table-header-cell {
    font-weight: bold;
    white-space: nowrap;
    /*background-color: rgb(31, 41, 55);*/
    /*color: white;*/
}

.group-table-even {
    color: rgb(31, 41, 55);
    background-color: white;
}

.group-table-odd {
    color: rgb(31, 41, 55);
    background-color: rgb(239, 246, 255);
}

.group-table-currency {
    text-align: right;
}
</style>
