<template>
    <div class="timeline-item" :class="itemClass">
        <div class="timeline-marker" :class="markerClass">
            <font-awesome-icon
                    v-if="hasIcon"
                    class="is-size-7"
                    :icon="subjectTypeIcons[activity.normalized_subject]" />
        </div>
        <div class="timeline-content">
            <p class="heading has-text-grey">{{ formatToLocalDateTime(activity.created_at) }}</p>
            <p>
                <span class="is-capitalized" v-if="hasCauser && !user">
                    {{ activity.normalized_causer }} #{{activity.causer_id}}
                    <span class="has-text-weight-semibold" v-if="causerName">{{ causerName }} </span>
                </span>
                <span class="is-capitalized" v-else-if="hasCauser || user">{{ activity.description }} </span>
                <span v-if="hasSubject && !hasCauser">
                    <span class="is-capitalized">{{ activity.normalized_subject }}</span> #{{ activity.subject_id }}
                    <span class="has-text-weight-semibold" v-if="subjectName">{{ subjectName }} </span>
                </span>
                <span v-if="!hasCauser && !user">was </span>
                <span v-if="!user">{{ activity.description }} </span>
                <span v-if="hasSubject && hasCauser">
                    {{ activity.normalized_subject }} #{{ activity.subject_id }}
                    <span class="has-text-weight-semibold" v-if="subjectName">{{ subjectName }} </span>
                </span>
                <span v-if="hasProperties">
                    <span v-for="(data, property) in activity.properties">
                        <span>{{ property }} <span class="has-text-weight-semibold">{{ Object.values(data).join(', ') }}</span></span>
                    </span>
                </span>
            </p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ActivityItem",

        props: {
            activity: {
                type: Object,
                required: true
            },
            user: {
                type: [String, Number],
                default: null
            },
        },

        data() {
            return {
                subjectTypeIcons: {
                    'backup': ['fas', 'archive'],
                    'task': ['fas', 'tasks'],
                    'database': ['fas', 'database'],
                    'destination': ['fas', 'cloud'],
                    'user': ['fas', 'users'],
                    'settings': ['fas', 'cog'],
                },
                descriptionClasses: {
                    'created': 'is-success',
                    'deleted': 'is-warning',
                    'updated': 'is-info',
                },
                descriptionTextClasses: {
                    'created': 'has-text-white',
                    'updated': 'has-text-white',
                },
            }
        },

        computed: {
            itemClass() {
                return this.descriptionClasses[this.activity.description] || '';
            },

            markerClass() {
                return [
                    {'is-icon': this.hasIcon},
                    this.itemClass,
                    this.descriptionTextClasses[this.activity.description] || 'has-text-grey'
                ];
            },

            hasIcon() {
                return !!this.subjectTypeIcons[this.activity.normalized_subject];
            },

            hasSubject() {
                return this.activity.normalized_subject && this.activity.subject_id || false;
            },

            hasCauser() {
                return (this.activity.normalized_causer && this.activity.causer_id) &&
                    !(this.activity.normalized_causer === 'user' && this.activity.causer_id == this.user) || false;
            },

            hasProperties() {
                return this.activity.properties || false;
            },

            subjectName() {
                if (this.activity.subject) {
                    return this.activity.subject.name || this.activity.subject.key || '';
                }

                return '';
            },

            causerName() {
                if (this.activity.causer) {
                    return this.activity.causer.name || '';
                }

                return '';
            },
        },
    }
</script>