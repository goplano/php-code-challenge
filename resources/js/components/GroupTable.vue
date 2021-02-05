<template>
    <div class="w-full">
        <table class="collapse border">
            <tr v-for="item in groupedData">
                <td v-for="field in fields" class="border px-3 py-1">{{ format(field, item[field]) }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
import accounting from "accounting";

export default {
    name: "MyTable",
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
        formats: {
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
        makeRow(defaults = {}) {
            let obj = {};
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
            if (value === '' || this.fields.includes(value) ) return value;
            if (this.formats.includes(field)) {
                return this.formatMoney(value);
            }
            return value;
        },
        formatMoney(value) {
            return accounting.formatMoney(value);
        },
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
                        // console.log(totals);
                    }

                    if (result.length > 0) {
                        if (this.hasTotals) {
                            let defaults = {};
                            defaults[this.groupHeaderColumn] = 'Totals';
                            this.totals.forEach((field) => defaults[field] = totals[currGroup][field]);
                            // console.log(totals, this.makeRow(defaults));

                            result.push(this.makeRow(defaults));
                        }
                        result.push(this.makeRow());
                    }
                    currGroup = item[this.groupBy];
                    // print header
                    let header = {};
                    this.fields.forEach((field) => header[field] = field);
                    header[(this.groupHeaderColumn !== null) ? this.groupHeaderColumn : this.groupBy] = currGroup;
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
                    // console.log(totals, this.makeRow(defaults));
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

<style scoped>

</style>
