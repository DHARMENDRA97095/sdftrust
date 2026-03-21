import { Link } from 'react-router-dom';


const Footer = () => {
  return (
    <footer className="bg-primary text-white pt-16 pb-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          {/* Organization Info */}
          <div>
            <img className=" mb-4 " src='logo/logo.png'></img>
            <p className="text-gray-100 mb-6 text-sm leading-relaxed">
              Empowering communities and sustaining the future through integrated development programs across health, education, and environment.
            </p>
            <div className="flex space-x-4">
              <a href="#" className="bg-white/10 hover:bg-secondary w-8 h-8 rounded-full flex items-center justify-center text-white transition-all duration-300">f</a>
              <a href="#" className="bg-white/10 hover:bg-secondary w-8 h-8 rounded-full flex items-center justify-center text-white transition-all duration-300">𝕏</a>
              <a href="#" className="bg-white/10 hover:bg-secondary w-8 h-8 rounded-full flex items-center justify-center text-white transition-all duration-300">in</a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-lg font-bold mb-4">Quick Links</h4>
            <ul className="space-y-3 text-sm">
              <li><Link to="/about" className="hover:text-secondary transition-colors">About Us</Link></li>
              <li><Link to="/programs" className="hover:text-secondary transition-colors">Our Programs</Link></li>
              <li><Link to="/projects" className="hover:text-secondary transition-colors">Ongoing Projects</Link></li>
              <li><Link to="/impact" className="hover:text-secondary transition-colors">Impact & Evidence</Link></li>
              <li><Link to="/contact" className="hover:text-secondary transition-colors">Contact Us</Link></li>
            </ul>
          </div>

          {/* Programs */}
          <div>
            <h4 className="text-lg font-bold mb-4">Our Programs</h4>
            <ul className="space-y-3 text-sm">
              <li><Link to="#" className="hover:text-secondary transition-colors">Health & Nutrition</Link></li>
              <li><Link to="#" className="hover:text-secondary transition-colors">Education & Child Care</Link></li>
              <li><Link to="#" className="hover:text-secondary transition-colors">Women Empowerment</Link></li>
              <li><Link to="#" className="hover:text-secondary transition-colors">Agriculture & Climate</Link></li>
              <li><Link to="#" className="hover:text-secondary transition-colors">Environment & WASH</Link></li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-lg font-bold mb-4">Contact Information</h4>
            <ul className="space-y-4 text-sm">
              <li className="flex items-start gap-3">
                <span className="shrink-0 mt-0.5 text-lg">📍</span>
                <span>123 Development Avenue, NGO District, City, 10001</span>
              </li>
              <li className="flex items-center gap-3">
                <span className="shrink-0 text-lg">📞</span>
                <span>+1 (555) 123-4567</span>
              </li>
              <li className="flex items-center gap-3">
                <span className="shrink-0 text-lg">✉️</span>
                <span>contact@sdfoundation.org</span>
              </li>
            </ul>
          </div>
        </div>

        <div className="border-t border-white/20 pt-8 mt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-200">
          <p>&copy; {new Date().getFullYear()} Sustainable Development Foundation. All rights reserved.</p>
          <div className="flex space-x-4 mt-4 md:mt-0">
            <Link to="/privacy" className="hover:text-white transition-colors">Privacy Policy</Link>
            <Link to="termcondition" className="hover:text-white transition-colors">Terms of Use</Link>
            <Link to="refundpolicy" className="hover:text-white transition-colors">Refund Policy</Link>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
