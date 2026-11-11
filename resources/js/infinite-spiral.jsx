import React from 'react';
import { createRoot } from 'react-dom/client';
import InfiniteSpiral from '../reactItems/InfiniteSpiral';

export function initInfiniteSpiral() {
    const rootEl = document.getElementById('infinite-spiral-root');
    if (!rootEl || rootEl._reactRoot) return;

    // Richly Styled SAP Module Cards matching ZebLearn brand & official taxonomy
    const modules = [
        { id: 'fico', code: 'FICO', title: 'SAP FICO', subtitle: 'Finance & Controlling', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'mm', code: 'MM', title: 'SAP MM', subtitle: 'Materials Management', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'sd', code: 'SD', title: 'SAP SD', subtitle: 'Sales & Distribution', tag: 'SALES', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'pp', code: 'PP', title: 'SAP PP', subtitle: 'Production Planning', tag: 'MFG', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 's4hana', code: 'S/4HANA', title: 'S/4HANA', subtitle: 'Next-Gen Cloud ERP', tag: 'CORE ERP', type: 'core', accent: '#FB6221', href: '#course-deck' },
        { id: 'abap', code: 'ABAP', title: 'SAP ABAP', subtitle: 'Advanced Business App', tag: 'DEV', type: 'tech', accent: '#A78BFA', href: '#course-deck' },
        { id: 'basis', code: 'BASIS', title: 'SAP BASIS', subtitle: 'System Administration', tag: 'INFRA', type: 'tech', accent: '#A78BFA', href: '#course-deck' },
        { id: 'hcm', code: 'HCM', title: 'SAP HCM', subtitle: 'Human Capital Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
        { id: 'ewm', code: 'EWM', title: 'SAP EWM', subtitle: 'Extended Warehouse', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'scm', code: 'SCM', title: 'SAP SCM', subtitle: 'Supply Chain Mgmt', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'ariba', code: 'Ariba', title: 'SAP Ariba', subtitle: 'Strategic Sourcing', tag: 'SOURCING', type: 'cloud', accent: '#FBBF24', href: '#course-deck' },
        { id: 'sf', code: 'SF', title: 'SuccessFactors', subtitle: 'Cloud HCM Suite', tag: 'CLOUD HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
        { id: 'fscm', code: 'FSCM', title: 'SAP FSCM', subtitle: 'Fin. Supply Chain', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'tr', code: 'TR', title: 'SAP TR', subtitle: 'Treasury & Risk Mgmt', tag: 'TREASURY', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'co', code: 'CO', title: 'SAP CO', subtitle: 'Cost Controlling', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'fi', code: 'FI', title: 'SAP FI', subtitle: 'Financial Accounting', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'aa', code: 'AA', title: 'SAP AA', subtitle: 'Asset Accounting', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'refx', code: 'RE-FX', title: 'SAP RE-FX', subtitle: 'Real Estate Mgmt', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'eccs', code: 'EC-CS', title: 'SAP EC-CS', subtitle: 'Consolidation Mgmt', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'tm', code: 'TM', title: 'SAP TM', subtitle: 'Transportation Mgmt', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'ibp', code: 'IBP', title: 'SAP IBP', subtitle: 'Integrated Business Plan', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'apo', code: 'APO', title: 'SAP APO', subtitle: 'Adv. Planning & Opt.', tag: 'SUPPLY CHAIN', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'bpc', code: 'BPC', title: 'SAP BPC', subtitle: 'Budget & Planning', tag: 'FINANCE', type: 'finance', accent: '#38BDF8', href: '#course-deck' },
        { id: 'le', code: 'LE', title: 'SAP LE', subtitle: 'Logistics Execution', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'srm', code: 'SRM', title: 'SAP SRM', subtitle: 'Supplier Relations', tag: 'SOURCING', type: 'cloud', accent: '#FBBF24', href: '#course-deck' },
        { id: 'wm', code: 'WM', title: 'SAP WM', subtitle: 'Warehouse Management', tag: 'LOGISTICS', type: 'logistics', accent: '#34D399', href: '#course-deck' },
        { id: 'lso', code: 'LSO', title: 'SAP LSO', subtitle: 'Learning Solution', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
        { id: 'om', code: 'OM', title: 'SAP OM', subtitle: 'Organizational Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
        { id: 'py', code: 'PY', title: 'SAP PY', subtitle: 'Payroll Management', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' },
        { id: 'pt', code: 'PT', title: 'SAP PT', subtitle: 'Personnel Time Mgmt', tag: 'HR', type: 'hr', accent: '#F472B6', href: '#course-deck' }
    ];

    const root = createRoot(rootEl);
    rootEl._reactRoot = root;
    root.render(
        <div style={{ height: '600px', width: '100%', position: 'relative', overflow: 'hidden' }}>
            <InfiniteSpiral
                items={modules}
                animationMode="all"
                speed={0.52}
                radius={176}
                cardWidth={124}
                cardHeight={100}
                verticalSpacing={58}
                perspective={1000}
                cardRadius={14}
                centerScale={1.22}
                edgeBlur={3}
                cardsPerTurn={7}
                pauseOnHover={true}
                direction="up"
                rotation={0}
                cardTilt={0}
                edgeFade={0.3}
                imageFit="cover"
                grayscale={0}
            />
        </div>
    );
}
