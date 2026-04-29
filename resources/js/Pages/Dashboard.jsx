import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Dashboard() {
    const { application, auth } = usePage().props;
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
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6 text-gray-900">
                            <div>
                                <h3 className="text-lg font-bold">
                                    Application Status:{' '}
                                    <span className="uppercase text-green-600">
                                        {statusLabels[status]}
                                    </span>
                                </h3>
                                {status === 'not_started' && (
                                    <p className="mt-2 text-gray-600">
                                        Start your scholarship application to provide your personal, financial, guardian, and essay details.
                                    </p>
                                )}
                                {status === 'draft' && (
                                    <p className="mt-2 text-gray-600">
                                        Your draft is saved. Continue where you stopped and submit when complete.
                                    </p>
                                )}
                                {status === 'submitted' && (
                                    <p className="mt-2 text-gray-600">
                                        Your application is submitted and waiting for committee review.
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
                                <Link href={route('application.form')}>
                                    <PrimaryButton>
                                        {status === 'not_started' ? 'Start Application' : 'Open My Application'}
                                    </PrimaryButton>
                                </Link>
                                
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
