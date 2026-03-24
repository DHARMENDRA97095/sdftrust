import { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";

const Publications = () => {
  const location = useLocation();
  const [publications, setPublications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

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

  useEffect(() => {
    const fetchPublications = async () => {
      try {
        const response = await fetch("http://localhost/backend/api/publications.php");
        if (!response.ok) {
          throw new Error("Failed to fetch publications");
        }
        const data = await response.json();
        if (data.status === "success") {
          setPublications(data.data);
        } else {
          throw new Error(data.message || "Error fetching publications");
        }
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchPublications();
  }, []);

  if (loading) {
    return (
      <div className="bg-bg-color min-h-screen py-20 flex items-center justify-center">
        <div className="text-xl text-primary font-bold animate-pulse">
          Loading Publications...
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-bg-color min-h-screen py-20 flex items-center justify-center">
        <div className="text-red-500 font-bold px-4 text-center">
            <p className="text-2xl mb-4">Oops! Something went wrong.</p>
            <p>{error}</p>
        </div>
      </div>
    );
  }

  const reports = publications.filter(p => p.type === 'report');
  const caseStudies = publications.filter(p => p.type === 'case_study');
  const galleries = publications.filter(p => p.type === 'gallery');

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
          {reports.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {reports.map((report) => (
              <a href={report.file_url} target="_blank" rel="noreferrer" key={report.id} className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between hover:shadow-md transition-shadow">
                 <div className="flex items-center gap-4">
                   <div className="w-12 h-12 bg-red-50 text-red-500 rounded flex items-center justify-center font-bold">PDF</div>
                   <div>
                     <h3 className="font-bold text-text-primary">{report.title}</h3>
                     {report.file_size && <p className="text-sm text-gray-500">{report.file_size}</p>}
                   </div>
                 </div>
                 <button className="text-primary hover:text-secondary font-bold text-xl">↓</button>
              </a>
            ))}
          </div>
          ) : (
             <p className="text-gray-500">No annual reports available.</p>
          )}
        </section>

        {/* Case Studies */}
        <section id="case-studies" className="scroll-mt-32">
          <div className="flex items-center gap-3 mb-8">
             <span className="text-3xl">📝</span>
             <h2 className="text-3xl font-serif text-text-primary">Case Studies</h2>
          </div>
          {caseStudies.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
             {caseStudies.map((study) => (
               <div key={study.id} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1 transition-transform flex flex-col">
                  <div className="h-40 bg-gray-200">
                    <img src={study.image_url || 'https://images.unsplash.com/photo-1544027993-37dbddc92582?q=80&w=400&auto=format&fit=crop'} alt={study.title} className="w-full h-full object-cover" />
                  </div>
                  <div className="p-6 flex-1 flex flex-col">
                    <span className="text-xs font-bold text-accent uppercase tracking-wider mb-2 block">{study.category}</span>
                    <h3 className="font-bold text-text-primary mb-4 flex-1">{study.title}</h3>
                    {study.file_url ? (
                        <a href={study.file_url} target="_blank" rel="noreferrer" className="text-primary hover:text-[#5a6425] text-sm font-bold flex items-center gap-1 mt-auto">Read Study <span className="text-lg">→</span></a>
                    ) : null}
                  </div>
               </div>
             ))}
          </div>
          ) : (
             <p className="text-gray-500">No case studies available.</p>
          )}
        </section>

        {/* Gallery */}
        <section id="galleries" className="scroll-mt-32">
          <div className="flex items-center gap-3 mb-8">
             <span className="text-3xl">🖼️</span>
             <h2 className="text-3xl font-serif text-text-primary">Photo Galleries</h2>
          </div>
          {galleries.length > 0 ? (
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
             {galleries.map((gallery) => (
               <div key={gallery.id} className="aspect-square bg-gray-200 rounded-lg overflow-hidden group cursor-pointer relative">
                 <img src={gallery.image_url} alt="Gallery" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                 <div className="absolute inset-0 bg-primary/0 group-hover:bg-primary/40 transition-colors flex items-center justify-center">
                    <span className="text-white opacity-0 group-hover:opacity-100 text-3xl transition-opacity">👁️</span>
                 </div>
               </div>
             ))}
          </div>
          ) : (
             <p className="text-gray-500">No photo galleries available.</p>
          )}
        </section>

      </div>
    </div>
  );
};

export default Publications;
