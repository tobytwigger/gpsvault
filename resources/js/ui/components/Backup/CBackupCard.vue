<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
        <v-card-title>
            {{backup.title}}
        </v-card-title>

        <v-card-subtitle v-if="backup.caption">
            {{ backup.caption }}
        </v-card-subtitle>

        <v-card-text>
            <v-chip
                outlined
                small
                class="ma-2"
                color="indigo"
            >
                <v-icon left>
                    mdi-calendar
                </v-icon>
                {{toDateTime(backup.created_at)}}
            </v-chip>
            <v-chip
                outlined
                small
                class="ma-2"
                color="indigo"
            >
                <v-icon left>
                    mdi-sd
                </v-icon>
                {{toSize(backup.size)}}
            </v-chip>
        </v-card-text>

<!--            <v-list-item-avatar-->
<!--                tile-->
<!--                size="80"-->
<!--                color="grey"-->
<!--            ></v-list-item-avatar>-->
<!--        </v-list-item>-->

        <v-card-actions>

            <v-spacer></v-spacer>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            icon
                            :href="ziggyRoute('file.download', backup.id)"
                            link
                            v-bind="attrs"
                            v-on="on"
                        >
                            <v-icon>mdi-download</v-icon>
                        </v-btn>
                    </template>
                    Download
                </v-tooltip>

                <c-backup-form :old-backup="backup" title="Edit backup" button-text="Update">
                    <template v-slot:activator="{trigger,showing}">
                        <v-btn
                            icon
                            @click="trigger"
                            :disabled="showing"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </template>
                </c-backup-form>

                <c-confirmation-dialog button-text="Delete" :loading="deleting" title="Delete backup?" @confirm="deleteBackup">
                    <template v-slot:activator="{trigger,showing}">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    @click="trigger"
                                    :disabled="showing"
                                    link
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                            Delete
                        </v-tooltip>
                    </template>
                    Are you sure you want to delete this backup? Once it is deleted you won't be able to download it, so make sure you have it downloaded.
                </c-confirmation-dialog>

            </v-card-actions>
        </v-card-actions>

    </v-card>
</template>

<script>
import moment from 'moment';
import * as convertUnit from 'convert-units';
import CConfirmationDialog from '../CConfirmationDialog';
import CBackupForm from './CBackupForm';


export default {
    name: "CBackupCard",
    components: {CBackupForm, CConfirmationDialog},
    props: {
        backup: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showExtra: false,
            deleting: false
        }
    },
    methods: {
        deleteBackup() {
            this.deleting = true;
            this.$inertia.delete(ziggyRoute('backups.destroy', this.backup.id), {
                onFinish: () => this.deleting = false
            })
        },
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY HH:mm');
        },
        toSize(value) {
            if (value === null) {
                return 'No Size';
            }
            let conversion = convertUnit(value).from('b').toBest();
            return (Math.round((conversion.val + Number.EPSILON) * 100) / 100) + ' ' + conversion.unit;
        },
    }
}
</script>

<style scoped>

</style>
