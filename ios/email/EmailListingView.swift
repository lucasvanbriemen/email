//
//  ContentView.swift
//  email
//
//  Created by Lucas on 04/05/2026.
//

import SwiftUI

struct EmailListingView: View {
    let group: String
    
    @State var emails: [Email] = []
    
    var body: some View {
        NavigationStack {
            List() {
                ForEach(emails) { email in
                    NavigationLink(destination: EmailView(email: email.id)) {
                        HStack() {
                            AsyncImage(url: email.sender.imageURL) { phase in
                                switch phase {
                                case .success(let image):
                                    image.resizable()
                                case .failure:
                                    Image(systemName: "person.fill").resizable()
                                @unknown default:
                                    Image(systemName: "person.fill").resizable()
                                }
                            }
                            .scaledToFit()
                            .frame(width: 40, height: 40)
                            .clipShape(Circle())
                            
                            VStack(alignment: .leading) {
                                Text(email.subject)
                                    .font(.headline)
                                Text(email.senderName)
                            }
                        }
                    }
                }
            }
        }
        
        
        
        .task {
            await getGroups()
        }
    }
    
    func getGroups() async {
        let url = URL(string: "https://email.lucasvanbriemen.nl/api/mailbox/" + group)!
        let devToken = "DEV_TOKEN"
        
        var request = URLRequest(url: url)
        request.setValue("Bearer \(devToken)", forHTTPHeaderField: "Authorization")
        
        do {
            let (data, _) = try await URLSession.shared.data(for: request)
            let decoded = try JSONDecoder().decode(EmailListResponse.self, from: data)
            emails = decoded.data
        } catch {
            print(">>> Decode failed: \(error)")
        }
    }
}
