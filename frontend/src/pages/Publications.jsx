import { useEffect } from "react";
import { useLocation } from "react-router-dom";

const Publications = () => {



   const location = useLocation(); // 2. Define location here

  useEffect(() => {
    if (location.hash) {
      const el = document.querySelector(location.hash);
      if (el) {
        setTimeout(() => {
          el.scrollIntoView({ behavior: "smooth" });
        }, 100);
      }
    }
  }, [location]);
  return (
    <div className="bg-bg-color min-h-screen">
      <section className="bg-primary text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">Publications & Resources</h1>
          <p className="text-xl max-w-2xl mx-auto text-primary-50">Explore our annual reports, case studies, and gallery of impact-driven work.</p>
        </div>
      </section>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-24">
        {/* Annual Reports */}
        <section id="annual-reports" className="scroll-mt-32">
          <div className="flex items-center gap-3 mb-8">
             <span className="text-3xl">📊</span>
             <h2 className="text-3xl font-serif text-text-primary">Annual Reports</h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {[2023, 2022, 2021].map(year => (
              <div key={year} className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition-shadow">
                 <div className="flex items-center gap-4">
                   <div className="w-12 h-12 bg-red-50 text-red-500 rounded flex items-center justify-center font-bold">PDF</div>
                   <div>
                     <h3 className="font-bold text-text-primary">Impact Report {year}</h3>
                     <p className="text-sm text-gray-500">2.4 MB</p>
                   </div>
                 </div>
                 <button className="text-primary hover:text-secondary font-bold text-xl">↓</button>
              </div>
            ))}
          </div>
        </section>

        {/* Case Studies */}
        <section id="case-studies" className="scroll-mt-32">
          <div className="flex items-center gap-3 mb-8">
             <span className="text-3xl">📝</span>
             <h2 className="text-3xl font-serif text-text-primary">Case Studies</h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
             {[
               { title: "Reviving Dead Lakes in Rajasthan", category: "Environment" },
               { title: "Digital Literacy for Tribal Youth", category: "Education" },
               { title: "Micro-financing Women Entrepreneurs", category: "Livelihoods" }
             ].map((study, idx) => (
               <div key={idx} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1 transition-transform">
                  <div className="h-40 bg-gray-200">
                    <img src={`https://images.unsplash.com/photo-1544027993-37dbddc92582?q=80&w=400&auto=format&fit=crop&sig=${idx}`} alt={study.title} className="w-full h-full object-cover" />
                  </div>
                  <div className="p-6">
                    <span className="text-xs font-bold text-accent uppercase tracking-wider mb-2 block">{study.category}</span>
                    <h3 className="font-bold text-text-primary mb-4">{study.title}</h3>
                    <button className="text-primary hover:text-[#5a6425] text-sm font-bold flex items-center gap-1">Read Study <span className="text-lg">→</span></button>
                  </div>
               </div>
             ))}
          </div>
        </section>

        {/* Gallery */}
        <section id="galleries" className="scroll-mt-32">
          <div className="flex items-center gap-3 mb-8">
             <span className="text-3xl">🖼️</span>
             <h2 className="text-3xl font-serif text-text-primary">Photo Galleries</h2>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
             {[1,2,3,4,5,6,7,8].map(num => (
               <div key={num} className="aspect-square bg-gray-200 rounded-lg overflow-hidden group cursor-pointer relative">
                 <img src={`https://images.unsplash.com/photo-1551000673-05b1c5a924ab?q=80&w=400&auto=format&fit=crop&sig=${num}`} alt={`Gallery ${num}`} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                 <div className="absolute inset-0 bg-primary/0 group-hover:bg-primary/40 transition-colors flex items-center justify-center">
                    <span className="text-white opacity-0 group-hover:opacity-100 text-3xl transition-opacity">👁️</span>
                 </div>
               </div>
             ))}
          </div>
        </section>

      </div>
    </div>
  );
};

export default Publications;
