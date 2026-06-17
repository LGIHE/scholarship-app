import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Dashboard() {
    const { application, auth, deadlinePassed, applicationDeadline } = usePage().props;
    const status = application?.status || 'not_started';
    const isScholar = auth?.user?.roles?.some(role => role.name === 'Scholar');

    const statusLabels = {
        not_started: 'Not Started',
        draft: 'Draft',
        submitted: 'Submitted',
        under_review: 'Under Review',
        approved: 'Approved',
        rejected: 'Rejected',
    };

    const formattedDeadline = applicationDeadline
        ? new Date(applicationDeadline + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
        : null;

    // canEdit: draft and submitted are both editable before the deadline
    const canEdit = !deadlinePassed && (status === 'draft' || status === 'submitted' || status === 'not_started');

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Applicant Portal
                </h2>
            }
        >
            <Head title="Applicant Portal" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    {/* Deadline banner */}
                    {deadlinePassed ? (
                        <div className="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-800">
                            <p className="font-semibold">Application deadline has passed</p>
                            <p>The deadline was {formattedDeadline}. Applications can no longer be submitted or edited.</p>
                        </div>
                    ) : formattedDeadline && (
                        <div className="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            <p className="font-semibold">Application deadline: {formattedDeadline}</p>
                            <p>Submit your application before this date. No applications will be accepted after this deadline.</p>
                        </div>
                    )}

                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 text-gray-900">
                            <div>
                                <h3 className="text-lg font-bold">
                                    Application Status:{' '}
                                    <span className="uppercase text-green-600">
                                        {statusLabels[status]}
                                    </span>
                                </h3>
                                {status === 'not_started' && !deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        Start your scholarship application to provide your personal, financial, guardian, and essay details.
                                    </p>
                                )}
                                {status === 'not_started' && deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        The application deadline has passed. You did not submit an application for this cycle.
                                    </p>
                                )}
                                {status === 'draft' && !deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        Your draft is saved. Continue where you stopped and submit when complete.
                                    </p>
                                )}
                                {status === 'draft' && deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        The deadline has passed and your application was not submitted. It is now read-only.
                                    </p>
                                )}
                                {status === 'submitted' && !deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        Your application has been submitted. You can still make changes and resubmit before the deadline on {formattedDeadline}.
                                    </p>
                                )}
                                {status === 'submitted' && deadlinePassed && (
                                    <p className="mt-2 text-gray-600">
                                        Your application was submitted and is now waiting for committee review. The deadline has passed and no further edits are allowed.
                                    </p>
                                )}
                                {status === 'under_review' && (
                                    <p className="mt-2 text-gray-600">
                                        The committee is currently reviewing your submission.
                                    </p>
                                )}
                                {status === 'approved' && (
                                    <p className="mt-2 text-gray-600">
                                        Congratulations. You have been approved for the scholarship.
                                    </p>
                                )}
                                {status === 'rejected' && (
                                    <p className="mt-2 text-gray-600">
                                        Your application was not approved in this cycle.
                                    </p>
                                )}
                            </div>

                            <div className="mt-6 flex gap-3">
                                {status !== 'not_started' && (
                                    <Link href={route('application.form')}>
                                        <PrimaryButton>
                                            {canEdit && status === 'submitted' ? 'Edit My Application' : canEdit ? 'Continue Application' : 'View My Application'}
                                        </PrimaryButton>
                                    </Link>
                                )}
                                {status === 'not_started' && !deadlinePassed && (
                                    <Link href={route('application.form')}>
                                        <PrimaryButton>Start Application</PrimaryButton>
                                    </Link>
                                )}

                                {isScholar && (
                                    <Link href={route('academic-progress.index')}>
                                        <PrimaryButton>
                                            Update Academic Progress
                                        </PrimaryButton>
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
