import { useEffect, useState } from "react";
import { useLocation, Link } from "react-router-dom";

const Programs = () => {
  const location = useLocation();
  const [programsList, setProgramsList] = useState([]);
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
    const fetchPrograms = async () => {
      try {
        const response = await fetch("http://localhost/sdftrust/backend/api/programs.php");

        if (!response.ok) {
          throw new Error("Failed to fetch programs");
        }

        const data = await response.json();

        if (data.status === "success") {
          setProgramsList(data.data);
        } else {
          throw new Error(data.message || "Error fetching programs");
        }
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchPrograms();
  }, []);

  if (loading) {
    return (
      <div className="bg-bg-color min-h-screen py-20 flex items-center justify-center">
        <div className="text-xl text-primary font-bold animate-pulse">
          Loading Programs...
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

  return (
    <div className="bg-bg-color min-h-screen pb-20">
      <section className="bg-secondary text-white py-20 bg-opacity-90 relative">
        <div className="absolute inset-0 z-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=2000')] bg-cover bg-center"></div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h1 className="text-4xl md:text-5xl font-serif font-bold mb-4">
            Our Programmes
          </h1>
          <p className="text-xl max-w-2xl mx-auto text-green-50">
            Impact-driven initiatives targeting the most critical challenges
            facing our communities today.
          </p>
        </div>
      </section>

      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {programsList.map((program, idx) => (
            <div
              key={program.id || idx}
              id={program.program_id}
              className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 group flex flex-col scroll-mt-28"
            >
              <div className="h-48 overflow-hidden">
                <img
                  src={program.image_url}
                  alt={program.title}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>

              <div className="p-8 relative grow flex flex-col">
                <div className="absolute -top-10 right-6 w-20 h-20 bg-white rounded-full flex items-center justify-center text-4xl shadow-xl border border-gray-50 hover:scale-110 transition-transform duration-300">
                  {program.icon || "📌"}
                </div>

                <h3 className="text-2xl font-serif font-bold text-text-primary mb-4 mt-4 leading-tight">
                  {program.title}
                </h3>

                <p className="text-gray-600 mb-6 grow">
                  {program.description}
                </p>

                <div className="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100 flex-wrap">
                  <span className="bg-[#E9EFE1] text-primary text-xs font-bold px-3 py-1 rounded-full">
                    {program.beneficiaries || "N/A"} Beneficiaries
                  </span>
                  <span className="bg-blue-50 text-accent text-xs font-bold px-3 py-1 rounded-full">
                    {program.regions || "N/A"} Active
                  </span>
                </div>

                <Link
                  to={`/programdetails/${program.slug}`}
                  className="text-primary font-bold hover:text-secondary uppercase tracking-wider text-sm self-start transition-colors mt-6"
                >
                  Explore Programme &rarr;
                </Link>
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
};

export default Programs;