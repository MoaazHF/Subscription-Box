<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tabs = [
            'resources' => [
                'title' => 'Resources',
                'description' => 'Operational knowledge base for teams managing subscription commerce at scale.',
                'topics' => [
                    [
                        'id' => 'articles',
                        'title' => 'Articles',
                        'summary' => 'Editorial documentation explaining platform decisions, execution patterns, and service expectations.',
                        'metadata' => '42 published articles · Updated weekly',
                        'details' => [
                            'Quarterly content roadmap tied to billing and fulfillment milestones.',
                            'Editorial review process with release-owner signoff.',
                            'Coverage for onboarding, retention, dispatch, and incident handling.',
                        ],
                    ],
                    [
                        'id' => 'product-updates',
                        'title' => 'Product updates',
                        'summary' => 'Release summaries for platform capabilities, policy updates, and workflow changes.',
                        'metadata' => '18 release notes this quarter',
                        'details' => [
                            'Versioned release cadence with deployment dates and rollback notes.',
                            'Change impact statements for operations, support, and customers.',
                            'Forward compatibility guidance for role-specific workflows.',
                        ],
                    ],
                    [
                        'id' => 'operations-playbooks',
                        'title' => 'Operations playbooks',
                        'summary' => 'Step-by-step execution guides for provisioning, route assignment, and escalation flow.',
                        'metadata' => '11 validated playbooks · SLA aligned',
                        'details' => [
                            'Standard operating procedures for daily and peak-month operations.',
                            'Escalation matrix mapped by ownership and severity level.',
                            'Checklist format used during warehouse and dispatch handoff.',
                        ],
                    ],
                    [
                        'id' => 'shipping-guides',
                        'title' => 'Shipping guides',
                        'summary' => 'Carrier-facing standards for packaging, route SLAs, and proof-of-delivery controls.',
                        'metadata' => 'Coverage across 3 shipping tiers',
                        'details' => [
                            'Packaging and labeling requirements by weight and tier.',
                            'Transit-time benchmarks and exception response windows.',
                            'Delivery proof and undeliverable recovery requirements.',
                        ],
                    ],
                ],
            ],
            'support' => [
                'title' => 'Support',
                'description' => 'Customer support documentation with clear response standards and service channels.',
                'topics' => [
                    [
                        'id' => 'faq',
                        'title' => 'FAQ',
                        'summary' => 'High-frequency customer questions with definitive policy-backed answers.',
                        'metadata' => 'Top 60 questions by contact volume',
                        'details' => [
                            'Subscription lifecycle answers from signup to cancellation.',
                            'Billing, refund, and renewal policy clarifications.',
                            'Delivery and claim timelines with expected outcomes.',
                        ],
                    ],
                    [
                        'id' => 'help-center',
                        'title' => 'Help center',
                        'summary' => 'Guided troubleshooting by role, workflow step, and error condition.',
                        'metadata' => 'Search-indexed knowledge base',
                        'details' => [
                            'Role-specific paths for subscriber, driver, and admin users.',
                            'Decision trees for payment failures and delivery exceptions.',
                            'Resolved issue library with reproducible remediation steps.',
                        ],
                    ],
                    [
                        'id' => 'support-tickets',
                        'title' => 'Support tickets',
                        'summary' => 'Ticket intake and queue policy with resolution SLAs and ownership routing.',
                        'metadata' => 'Average first response: 22 minutes',
                        'details' => [
                            'Priority model mapped to claim, billing, and delivery incidents.',
                            'Ownership assignment across support and operations teams.',
                            'Resolution quality checks before ticket closure.',
                        ],
                    ],
                    [
                        'id' => 'contact-email',
                        'title' => 'subscriptionboxplatform@gmail.com',
                        'summary' => 'Primary contact inbox for account and platform support inquiries.',
                        'metadata' => 'Monitored daily · Enterprise support window',
                        'details' => [
                            'Escalation-ready channel for unresolved support cases.',
                            'Structured intake format for faster triage and response.',
                            'Linked to incident reporting and audit retention workflow.',
                        ],
                    ],
                ],
            ],
            'company' => [
                'title' => 'Company',
                'description' => 'Corporate and legal documentation for trust, governance, and long-term operations.',
                'topics' => [
                    [
                        'id' => 'about',
                        'title' => 'About',
                        'summary' => 'Platform mission, scope, and operating principles for subscription commerce.',
                        'metadata' => 'Founded for operational reliability',
                        'details' => [
                            'Mission focused on resilient subscription execution.',
                            'Unified architecture spanning billing through delivery.',
                            'Governance model centered on accountability and auditability.',
                        ],
                    ],
                    [
                        'id' => 'careers',
                        'title' => 'Careers',
                        'summary' => 'Role families, hiring standards, and growth paths across engineering and operations.',
                        'metadata' => 'Open roles reviewed monthly',
                        'details' => [
                            'Hiring tracks for product, logistics, and support excellence.',
                            'Role expectations with measurable performance outcomes.',
                            'Career progression tied to operational impact.',
                        ],
                    ],
                    [
                        'id' => 'privacy-policy',
                        'title' => 'Privacy policy',
                        'summary' => 'Data collection, retention, and access rules for customer and operational records.',
                        'metadata' => 'Aligned with security and audit controls',
                        'details' => [
                            'Data minimization and retention boundaries by record type.',
                            'Access controls by role and workflow responsibility.',
                            'Compliance checkpoints for policy changes.',
                        ],
                    ],
                    [
                        'id' => 'terms-of-service',
                        'title' => 'Terms of service',
                        'summary' => 'Service terms, responsibilities, and dispute handling framework.',
                        'metadata' => 'Policy version 3.2',
                        'details' => [
                            'Contract terms for subscription and fulfillment services.',
                            'Service limitations and acceptable use boundaries.',
                            'Dispute resolution flow with documented milestones.',
                        ],
                    ],
                ],
            ],
        ];

        $activeTab = $request->string('tab')->toString();

        if (! array_key_exists($activeTab, $tabs)) {
            $activeTab = 'resources';
        }

        return view('docs.index', [
            'tabs' => $tabs,
            'activeTab' => $activeTab,
        ]);
    }
}
