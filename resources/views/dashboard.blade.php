<x-app-layout>
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); padding: 3rem 0 2.5rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 style="color: #fff; font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">
                Welcome back, <span style="color: #c9a227;">{{ Auth::user()->name }}</span>
            </h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 1rem;">
                Professional day count calculator for bonds, loans & derivatives
            </p>
        </div>
    </div>

    <div class="py-12" style="background: #f8fafc;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('calculator.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition" style="border-top: 3px solid #c9a227; text-decoration: none;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #0f172a, #1e3a5f); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <svg style="width: 24px; height: 24px; color: #c9a227;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Start Calculating</h3>
                    <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">Calculate day count factors and accrued interest for any convention</p>
                    <span style="color: #c9a227; font-weight: 600; font-size: 0.875rem;">Open Calculator →</span>
                </a>

                <a href="{{ route('comparison.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition" style="border-top: 3px solid #c9a227; text-decoration: none;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #0f172a, #1e3a5f); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <svg style="width: 24px; height: 24px; color: #c9a227;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Compare Conventions</h3>
                    <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">Run side-by-side comparisons of multiple conventions</p>
                    <span style="color: #c9a227; font-weight: 600; font-size: 0.875rem;">Open Comparison →</span>
                </a>

                <a href="{{ route('calculator.history') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition" style="border-top: 3px solid #c9a227; text-decoration: none;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #0f172a, #1e3a5f); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <svg style="width: 24px; height: 24px; color: #c9a227;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">View History</h3>
                    <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin-bottom: 1rem;">Access your recent calculations and saved records</p>
                    <span style="color: #c9a227; font-weight: 600; font-size: 0.875rem;">View History →</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
