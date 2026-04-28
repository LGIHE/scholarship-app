import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Index() {
    const { scholar, progressRecords } = usePage().props;
    const [showForm, setShowForm] = useState(false);
    const [editingRecord, setEditingRecord] = useState(null);

    const { data, setData, post, patch, processing, errors, reset } = useForm({
        academic_year: '',
        semester: '',
        gpa: '',
        cgpa: '',
        courses_taken: '',
        achievements: '',
        challenges: '',
        notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();

        if (editingRecord) {
            patch(route('academic-progress.update', editingRecord.id), {
                onSuccess: () => {
                    reset();
                    setShowForm(false);
                    setEditingRecord(null);
                },
            });
        } else {
            post(route('academic-progress.store'), {
                onSuccess: () => {
                    reset();
                    setShowForm(false);
                },
            });
        }
    };

    const handleEdit = (record) => {
        setData({
            academic_year: record.academic_year,
            semester: record.semester,
            gpa: record.gpa,
            cgpa: record.cgpa,
            courses_taken: record.courses_taken || '',
            achievements: record.achievements || '',
            challenges: record.challenges || '',
            notes: record.notes || '',
        });
        setEditingRecord(record);
        setShowForm(true);
    };

    const handleCancel = () => {
        reset();
        setShowForm(false);
        setEditingRecord(null);
    };

    if (!scholar) {
        return (
            <AuthenticatedLayout
                header={
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Academic Progress
                    </h2>
                }
            >
                <Head title="Academic Progress" />

                <div className="py-12">
                    <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                            <div className="rounded-md border border-amber-300 bg-amber-50 p-4">
                                <p className="text-sm text-amber-800">
                                    Academic progress tracking is only available for approved scholars.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Academic Progress
                </h2>
            }
        >
            <Head title="Academic Progress" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div className="mb-6 flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">
                                    My Academic Progress
                                </h3>
                                <p className="mt-1 text-sm text-gray-600">
                                    Track and update your academic performance each semester
                                </p>
                            </div>
                            {!showForm && (
                                <PrimaryButton onClick={() => setShowForm(true)}>
                                    Add Progress Update
                                </PrimaryButton>
                            )}
                        </div>

                        {showForm && (
                            <div className="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-6">
                                <h4 className="mb-4 text-base font-semibold text-gray-900">
                                    {editingRecord ? 'Edit Progress Update' : 'New Progress Update'}
                                </h4>

                                <form onSubmit={handleSubmit} className="space-y-4">
                                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <InputLabel htmlFor="academic_year" value="Academic Year" />
                                            <TextInput
                                                id="academic_year"
                                                className="mt-1 block w-full"
                                                value={data.academic_year}
                                                onChange={(e) => setData('academic_year', e.target.value)}
                                                placeholder="e.g., 2025/2026"
                                                required
                                            />
                                            <InputError message={errors.academic_year} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="semester" value="Semester" />
                                            <select
                                                id="semester"
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                value={data.semester}
                                                onChange={(e) => setData('semester', e.target.value)}
                                                required
                                            >
                                                <option value="">Select semester</option>
                                                <option value="Semester 1">Semester 1</option>
                                                <option value="Semester 2">Semester 2</option>
                                                <option value="Year 1">Year 1</option>
                                                <option value="Year 2">Year 2</option>
                                                <option value="Year 3">Year 3</option>
                                                <option value="Year 4">Year 4</option>
                                            </select>
                                            <InputError message={errors.semester} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="gpa" value="GPA (This Period)" />
                                            <TextInput
                                                id="gpa"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="5"
                                                className="mt-1 block w-full"
                                                value={data.gpa}
                                                onChange={(e) => setData('gpa', e.target.value)}
                                                required
                                            />
                                            <InputError message={errors.gpa} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="cgpa" value="CGPA (Cumulative)" />
                                            <TextInput
                                                id="cgpa"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="5"
                                                className="mt-1 block w-full"
                                                value={data.cgpa}
                                                onChange={(e) => setData('cgpa', e.target.value)}
                                                required
                                            />
                                            <InputError message={errors.cgpa} className="mt-2" />
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="courses_taken" value="Courses Taken (Optional)" />
                                        <textarea
                                            id="courses_taken"
                                            rows={3}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            value={data.courses_taken}
                                            onChange={(e) => setData('courses_taken', e.target.value)}
                                            placeholder="List the courses you took this semester"
                                        />
                                        <InputError message={errors.courses_taken} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="achievements" value="Achievements (Optional)" />
                                        <textarea
                                            id="achievements"
                                            rows={3}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            value={data.achievements}
                                            onChange={(e) => setData('achievements', e.target.value)}
                                            placeholder="Any academic achievements, awards, or recognitions"
                                        />
                                        <InputError message={errors.achievements} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="challenges" value="Challenges (Optional)" />
                                        <textarea
                                            id="challenges"
                                            rows={3}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            value={data.challenges}
                                            onChange={(e) => setData('challenges', e.target.value)}
                                            placeholder="Any challenges or difficulties faced"
                                        />
                                        <InputError message={errors.challenges} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="notes" value="Additional Notes (Optional)" />
                                        <textarea
                                            id="notes"
                                            rows={3}
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            value={data.notes}
                                            onChange={(e) => setData('notes', e.target.value)}
                                            placeholder="Any other information you'd like to share"
                                        />
                                        <InputError message={errors.notes} className="mt-2" />
                                    </div>

                                    <div className="flex gap-3">
                                        <PrimaryButton type="submit" disabled={processing}>
                                            {editingRecord ? 'Update Progress' : 'Save Progress'}
                                        </PrimaryButton>
                                        <SecondaryButton type="button" onClick={handleCancel}>
                                            Cancel
                                        </SecondaryButton>
                                    </div>
                                </form>
                            </div>
                        )}

                        {progressRecords.length === 0 ? (
                            <div className="rounded-md border border-gray-200 bg-gray-50 p-8 text-center">
                                <p className="text-gray-600">
                                    No progress updates yet. Click "Add Progress Update" to get started.
                                </p>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {progressRecords.map((record) => (
                                    <div
                                        key={record.id}
                                        className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div className="mb-3 flex items-start justify-between">
                                            <div>
                                                <h4 className="text-base font-semibold text-gray-900">
                                                    {record.academic_year} - {record.semester}
                                                </h4>
                                                <div className="mt-2 flex gap-4 text-sm">
                                                    <span className="text-gray-600">
                                                        <span className="font-medium">GPA:</span> {record.gpa}
                                                    </span>
                                                    <span className="text-gray-600">
                                                        <span className="font-medium">CGPA:</span> {record.cgpa}
                                                    </span>
                                                </div>
                                            </div>
                                            <button
                                                onClick={() => handleEdit(record)}
                                                className="text-sm text-indigo-600 hover:text-indigo-800"
                                            >
                                                Edit
                                            </button>
                                        </div>

                                        {record.courses_taken && (
                                            <div className="mt-3">
                                                <p className="text-xs font-medium text-gray-500">Courses Taken</p>
                                                <p className="mt-1 text-sm text-gray-700 whitespace-pre-wrap">
                                                    {record.courses_taken}
                                                </p>
                                            </div>
                                        )}

                                        {record.achievements && (
                                            <div className="mt-3">
                                                <p className="text-xs font-medium text-gray-500">Achievements</p>
                                                <p className="mt-1 text-sm text-gray-700 whitespace-pre-wrap">
                                                    {record.achievements}
                                                </p>
                                            </div>
                                        )}

                                        {record.challenges && (
                                            <div className="mt-3">
                                                <p className="text-xs font-medium text-gray-500">Challenges</p>
                                                <p className="mt-1 text-sm text-gray-700 whitespace-pre-wrap">
                                                    {record.challenges}
                                                </p>
                                            </div>
                                        )}

                                        {record.notes && (
                                            <div className="mt-3">
                                                <p className="text-xs font-medium text-gray-500">Notes</p>
                                                <p className="mt-1 text-sm text-gray-700 whitespace-pre-wrap">
                                                    {record.notes}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
